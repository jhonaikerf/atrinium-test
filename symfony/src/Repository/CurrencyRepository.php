<?php

namespace App\Repository;

use App\Entity\Currency;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Currency|null find($id, $lockMode = null, $lockVersion = null)
 * @method Currency|null findOneBy(array $criteria, array $orderBy = null)
 * @method Currency[]    findAll()
 * @method Currency[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CurrencyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Currency::class);
    }

    public function setInactives(array $isos = []): int
    {
        return $this->createQueryBuilder('currency')
            ->update()
            ->where("currency.iso NOT IN (:isos)")
            ->setParameter('isos', array_values($isos))
            ->set('currency.active', 0)
            ->getQuery()
            ->execute();
    }

    public function findByIsos(array $isos = [])
    {
        return $this->createQueryBuilder('currency')
            ->where("currency.iso IN (:isos)")
            ->setParameter('isos', array_values($isos))
            ->getQuery()
            ->getResult();
    }
}
