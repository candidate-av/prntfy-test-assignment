<?php

namespace App\Repository;

use App\Entity\ProductColor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ProductColor|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductColor|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductColor[]    findAll()
 * @method ProductColor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductColorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductColor::class);
    }
}
