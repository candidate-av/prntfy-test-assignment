<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Entity\Product;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Service\IpToGeoLocation\IpToGeoLocation;
use Doctrine\Common\Persistence\ObjectManager;
use Exception;
use InvalidArgumentException;

class OrderService
{
    /**
     * @var IpToGeoLocation
     */
    private $ipToGeoLocation;

    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * OrderService constructor.
     * @param IpToGeoLocation $ipToGeoLocation
     * @param ProductRepository $productRepository
     * @param OrderRepository $orderRepository
     */
    public function __construct(
        IpToGeoLocation $ipToGeoLocation,
        ProductRepository $productRepository,
        OrderRepository $orderRepository
    )
    {
        $this->ipToGeoLocation = $ipToGeoLocation;
        $this->productRepository = $productRepository;
        $this->orderRepository = $orderRepository;
    }


    /**
     * @param $orderProducts
     * @param $clientIp
     * @return Order
     * @throws Exception
     */
    public function createOrder($orderProducts, $clientIp)
    {
        $order = new Order();
        $order->setCountry($this->ipToGeoLocation->getCountryCodeByIp($clientIp));

        foreach ($orderProducts as $orderProduct) {
            if (empty($orderProduct['productId']) || empty($orderProduct['quantity'])) {
                throw new InvalidArgumentException('"productId" and "quantity" are mandatory for this request');
            }

            $product = $this->getProduct($orderProduct['productId']);
            $orderProduct = new OrderProduct($product, $orderProduct['quantity']);
            $order->addOrderProduct($orderProduct);
        }

        return $order;
    }

    /**
     * @param integer $productId
     * @return Product
     */
    private function getProduct($productId): Product
    {
        $product = $this->productRepository->find($productId);

        if (empty($product)) {
            throw new InvalidArgumentException('Product with id does not exist');
        }

        return $product;
    }
}