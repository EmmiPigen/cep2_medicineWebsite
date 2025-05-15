<?php

namespace App\Background;

use App\Entity\MedikamentLog;
use App\Enum\Lokale;
use App\Repository\UserRepository;
use App\Repository\MedikamentLogRepository;
use App\Service\SmsService;
use Doctrine\ORM\EntityManagerInterface;

class MedicintjekRunner
{
    public function __construct(
        private UserRepository $userRepo,
        private EntityManagerInterface $em,
        private MedikamentLogRepository $logRepo,
        private SmsService $smsService
    ) {}

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
        $users = $this->userRepo->findAll();

        foreach ($users as $user) {
            foreach ($user->getMedikamentListes() as $med) {
                foreach ($med->getTidspunkterTages() as $tidspunkt) {
                    $planlagtTid = \DateTime::createFromFormat('H:i', $tidspunkt);
                    if (!$planlagtTid) continue;

                    $planlagtTid->setDate((int)$now->format('Y'), (int)$now->format('m'), (int)$now->format('d'));

                    if ($now < $planlagtTid) continue;

                   // Hent kun dagens logs for brugeren og den aktuelle medicin
                    $logs = $this->logRepo->findTodayLogsFor($user, $med->getMedikamentNavn());

                    $matchFundet = false;
                    foreach ($logs as $log) {
                        $logTid = $log->getTagetTid();
                        if (!$logTid) continue;
                        if (
                            $log->getTagetStatus() === 'taget' 
                            // &&
                            // abs($logTid->getTimestamp() - $planlagtTid->getTimestamp()) <= 900 // 15 min
                        ) {
                            $matchFundet = true;
                            break;
                        }
                        if (
                            $log->getTagetStatus() === 'glemt'
                            //  &&
                            // $sekunderFraPlanlagt <= 900
                        ) {
                            $matchFundet = true;
                            break;
                        }
                    }

                    if (!$matchFundet && $user->getOmsorgspersonTelefon()) {
                        $this->smsService->sendSms(
                            '+4521900301',
                            'OBS: ' . $user->getFuldeNavn() . ' har ikke taget medicinen "' . $med->getMedikamentNavn() . '" kl. ' . $tidspunkt
                        );
                    }
                }
            }
        }

        $this->em->flush();
        echo "[" . $now->format('H:i:s') . "] Medicin-tjek udført\n";
    }
}
