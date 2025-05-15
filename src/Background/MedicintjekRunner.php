<?php

namespace App\Background;

use App\Entity\MedikamentLog;
use App\Entity\User;
use App\Entity\MedikamentListe;
use App\Entity\MedikamentAlarmLog;
use App\Enum\Lokale;
use App\Repository\UserRepository;
use App\Repository\MedikamentLogRepository;
use App\Service\SmsService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class MedicintjekRunner
{
    public function __construct(
        EntityManagerInterface $em,
        SmsService $smsService,
        LoggerInterface $logger
    ) {
        $this->em = $em;
        $this->smsService = $smsService;
        $this->logger = $logger;
    }

    public function run(): void
    {
        while (true) {
            $this->tjekMedicin();
            sleep(60); // vent 60 sekunder mellem tjek
        }
    }

    private function tjekMedicin(): void
    {
        $now = new \DateTime();
        $users = $this->em->getRepository(User::class)->findAll();

        foreach ($users as $user) {
            foreach ($user->getMedikamentListes() as $med) {
                foreach ($med->getTidspunkterTages() as $tidspunkt) {
                    $planlagtTid = \DateTime::createFromFormat('H:i', $tidspunkt);
                    if (!$planlagtTid) continue;

                    $planlagtTid->setDate((int)$now->format('Y'), (int)$now->format('m'), (int)$now->format('d'));

                    if ($now < $planlagtTid) continue;

                   // Hent kun dagens logs for brugeren og den aktuelle medicin
                    $logs = $this->em->getRepository(MedikamentLog::class)
                        ->findTodayLogsFor($user, $med->getMedikamentNavn());

                    $matchFundet = false;
                    foreach ($logs as $log) {
                        $logTid = $log->getTagetTid();
                        if (!$logTid) continue;
                        if (
                            $log->getTagetStatus() === '1'  
                            // &&
                            // abs($logTid->getTimestamp() - $planlagtTid->getTimestamp()) <= 900 // 15 min
                        ) {
                            $matchFundet = true;
                            $this->logger->info(
                                'Medicin tjekket: ' . $user->getFuldeNavn() . ' - ' . $med->getMedikamentNavn() . ' - ' . $logTid->format('H:i')
                            );
                            break;
                        }
                        if (
                            $log->getTagetStatus() === '0'
                            //  &&
                            // $sekunderFraPlanlagt <= 900
                        ) {
                            $matchFundet = true;
                            $this->logger->info(
                                'Medicin ikke taget: ' . $user->getFuldeNavn() . ' - ' . $med->getMedikamentNavn() . ' - ' . $logTid->format('H:i')
                            );
                            break;
                        }
                    }

                    if ($matchFundet && $user->getOmsorgspersonTelefon() && $log->getTagetStatus() === '0') {
                        $this->smsService->sendSms(
                            '+4521900301',
                            'OBS: ' . $user->getFuldeNavn() . ' har ikke taget medicinen "' . $med->getMedikamentNavn() . '" kl. ' . $tidspunkt
                        );

                        $log = new MedikamentAlarmLog();
                        $log->setUser($user);
                        $log->setMedikamentNavn($med->getMedikamentNavn());
                        $log->setDato($planlagtTid);
                        $log->setTidspunkt($planlagtTid->format('H:i'));
                        $this->em->persist($log);
                        $this->logger->info(
                            'SMS sendt til omsorgsperson: ' . $user->getFuldeNavn() . ' - ' . $med->getMedikamentNavn() . ' - ' . $planlagtTid->format('H:i')
                        );
                    }
                }
            }
        }

        $this->em->flush();
        echo "[" . $now->format('H:i:s') . "] Medicin-tjek udført\n";
        
    }
}
