<?php

namespace App\Background;

use App\Entity\MedikamentLog;
use App\Service\SmsService;
use Doctrine\ORM\EntityManagerInterface;

class MedicintjekRunner
{
    public function __construct(
        private EntityManagerInterface $em,
        private SmsService $smsService
    ) {}

    public function run(): void
    {
        while (true) {
            $this->sendAlertsForMissedMedication();
            sleep(60);
        }
    }

    private function sendAlertsForMissedMedication(): void
    {
        $now = new \DateTime();
        echo "[" . $now->format('H:i:s') . "] Tjekker for glemte mediciner...\n";

        $repo = $this->em->getRepository(MedikamentLog::class);
        $logs = $repo->createQueryBuilder('m')
            ->where('m.tagetStatus = :status')
            ->andWhere('m.alarmSent = false')
            ->setParameter('status', 'glemt')
            ->getQuery()
            ->getResult();

        foreach ($logs as $log) {
            $user = $log->getUserId(); // assuming this is a relation
            $telefon = $user->getOmsorgspersonTelefon();

            if ($telefon) {
                $this->smsService->sendSms(
                    $telefon,
                    'OBS: ' . $user->getFuldeNavn() .
                    ' har ikke taget medicinen "' . $log->getMedikamentNavn()
                );
                echo "➤ SMS sendt til {$telefon} for {$log->getMedikamentNavn()}\n";

                $log->setAlarmSent(true);
                $this->em->persist($log);
            }
        }

        $this->em->flush();
    }
}