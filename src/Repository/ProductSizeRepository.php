<?php

namespace App\Repository;

use App\Entity\ProductSize;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ProductSize|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductSize|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductSize[]    findAll()
 * @method ProductSize[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductSizeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductSize::class);
    }
}
