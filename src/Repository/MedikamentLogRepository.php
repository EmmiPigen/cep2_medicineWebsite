<?php

namespace App\Repository;

use App\Entity\MedikamentLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\User;

/**
 * @extends ServiceEntityRepository<MedikamentLog>
 */
class MedikamentLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MedikamentLog::class);
    }

    /**
     * Hent alle logs for en given bruger og medicin i dag
     *
     * @param User $user
     * @param string $medikamentNavn
     * @return MedikamentLog[]
     */
    public function findTodayLogsFor(User $user, string $medikamentNavn): array
    {
        $qb = $this->createQueryBuilder('l');

        $startOfDay = (new \DateTime())->setTime(0, 0);
        $endOfDay   = (new \DateTime())->setTime(23, 59, 59);

        return $qb
            ->andWhere('l.userId = :user')
            ->andWhere('l.medikamentNavn = :navn')
            ->andWhere('l.tagetTid BETWEEN :start AND :end')
            ->setParameter('user', $user)
            ->setParameter('navn', $medikamentNavn)
            ->setParameter('start', $startOfDay)
            ->setParameter('end', $endOfDay)

            ->orderBy('l.tagetTid', 'ASC')
            ->getQuery()
            ->getResult();
    
    }

//    /**
//     * @return MedikamentLog[] Returns an array of MedikamentLog objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?MedikamentLog
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
