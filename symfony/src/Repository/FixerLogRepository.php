<?php

namespace App\Repository;

use App\Entity\FixerLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FixerLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method FixerLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method FixerLog[]    findAll()
 * @method FixerLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FixerLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FixerLog::class);
    }

    public function findOneByConvert(
        string $fromIso,
        string $toIso,
        string $date
    ): ?FixerLog {
        return $this->createQueryBuilder('log')
            ->innerJoin('log.fromCurrency', 'fromCurrency')
            ->innerJoin('log.toCurrency', 'toCurrency')
            ->where("log.date = :date")
            ->andWhere("fromCurrency.iso = :fromIso")
            ->andWhere("toCurrency.iso = :toIso")
            ->setParameter('date', $date)
            ->setParameter('fromIso', $fromIso)
            ->setParameter('toIso', $toIso)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
