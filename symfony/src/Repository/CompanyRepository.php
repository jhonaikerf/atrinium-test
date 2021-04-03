<?php

namespace App\Repository;

use App\Entity\Company;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

/**
 * @method Company|null find($id, $lockMode = null, $lockVersion = null)
 * @method Company|null findOneBy(array $criteria, array $orderBy = null)
 * @method Company[]    findAll()
 * @method Company[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyRepository extends ServiceEntityRepository
{
    protected $security;

    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, Company::class);
        $this->security = $security;
    }

    public function findAllQueryBuilder()
    {
        $builder = $this->createQueryBuilder('company')
            ->innerJoin('company.sector', 'sector');
        if($this->security->getUser() && !in_array('ROLE_ADMIN', $this->security->getUser()->getRoles())){
            $builder->innerJoin('sector.users', 'user')
                ->where('user.id = :user_id')
                ->setParameter('user_id', $this->security->getUser()->getId());
        }
        return $builder;
    }
}
