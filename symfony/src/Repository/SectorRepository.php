<?php

namespace App\Repository;

use App\Entity\Sector;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

/**
 * @method Sector|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sector|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sector[]    findAll()
 * @method Sector[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SectorRepository extends ServiceEntityRepository
{
    protected $security;

    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, Sector::class);
        $this->security = $security;
    }

    public function findAllQueryBuilder()
    {
        $builder = $this->createQueryBuilder('sector');
        if($this->security->getUser() && !in_array('ROLE_ADMIN', $this->security->getUser()->getRoles())){
            $builder->innerJoin('sector.users', 'user')
                ->where('user.id = :user_id')
                ->setParameter('user_id', $this->security->getUser()->getId());
        }
        return $builder;
    }
}
