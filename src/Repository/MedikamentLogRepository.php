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
