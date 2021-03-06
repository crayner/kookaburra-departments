<?php
/**
 * Created by PhpStorm.
 *
 * Kookaburra
 *
 * (c) 2018 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 23/11/2018
 * Time: 15:27
 */
namespace Kookaburra\Departments\Repository;

use Kookaburra\Departments\Entity\Department;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class DepartmentRepository
 * @package Kookaburra\Departments\Repository
 */
class DepartmentRepository extends ServiceEntityRepository
{
    /**
     * DepartmentRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Department::class);
    }

    /**
     * findPagination
     * @return mixed
     */
    public function findPagination()
    {
        return $this->createQueryBuilder('d')
            ->select(['d','s','p'])
            ->leftJoin('d.staff', 's')
            ->leftJoin('s.person', 'p')
            ->orderBy('d.name', 'ASC')
            ->addOrderBy('p.surname', 'ASC')
            ->addOrderBy('p.firstName', 'ASC')
            ->groupBy('d.id')
            ->getQuery()
            ->getResult();
    }
}
