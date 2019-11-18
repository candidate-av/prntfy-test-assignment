<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Entity\Product;
use App\Entity\ProductType;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\Expr;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * Get order count by country since certain datetime
     *
     * @param string $country
     * @param DateTime $sinceDateTime
     * @return integer
     * @throws NonUniqueResultException
     */
    public function getOrderCountByCountrySinceDateTime($country, DateTime $sinceDateTime)
    {
        return $this->createQueryBuilder('o')
            ->select('count(o.id)')
            ->where('o.country = :country')
            ->andWhere('o.createdAt >= :since')
            ->setParameter('country', $country)
            ->setParameter('since', $sinceDateTime)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param ProductType $productType
     * @return Order[]
     * @throws DBALException
     */
    public function getOrdersByProductType(ProductType $productType)
    {
        $orderIds = $this->getOrderIdsContainingProductType($productType->getId());

        return $this->findBy(['id' => $orderIds]);
    }

    /**
     * @param $productTypeId
     * @return array
     * @throws DBALException
     */
    private function getOrderIdsContainingProductType($productTypeId)
    {
        $query = $this->_em->getConnection()
            ->prepare(
                'SELECT distinct(op.product_order_id)
            FROM order_product op 
            INNER JOIN product p ON op.product_id = p.id
            WHERE p.type_id=:type_id'
        );
        $query->bindParam('type_id', $productTypeId);
        $query->execute();
        $result = $query->fetchAll();

        return array_column($result, 'product_order_id');
    }
}
