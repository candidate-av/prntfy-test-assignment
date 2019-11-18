<?php

namespace App\Service;

use App\Entity\Order;
use App\Exception\OrderCantBeCreatedException;
use App\Repository\OrderRepository;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Exception;

class OrderValidationService
{
    /**
     * Minimal allowed order total
     * @var int
     */
    private $minOrderTotal;

    /**
     * @var int
     */
    private $orderLimitForCountryPerTimeFrame;

    /**
     * @var string
     */
    private $orderLimitForCountryTimeFrame;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * OrderValidationService constructor.
     * @param integer|float $minOrderTotal
     * @param integer $orderLimitForCountryPerTimeFrame
     * @param string $orderLimitForCountryTimeFrame
     * @param OrderRepository $orderRepository
     */
    public function __construct(
        $minOrderTotal,
        $orderLimitForCountryPerTimeFrame,
        $orderLimitForCountryTimeFrame,
        OrderRepository $orderRepository
    )
    {
        $this->minOrderTotal = $minOrderTotal;
        $this->orderLimitForCountryPerTimeFrame = $orderLimitForCountryPerTimeFrame;
        $this->orderLimitForCountryTimeFrame = $orderLimitForCountryTimeFrame;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param Order $order
     * @throws NonUniqueResultException
     * @throws OrderCantBeCreatedException
     */
    public function checkOrderCanBePlaced(Order $order)
    {
       $this->checkMinOrderTotal($order->getTotalPrice());
       $this->checkOrderLimitPerCountry($order->getCountry());
    }

    /**
     * @param $orderTotal
     * @throws OrderCantBeCreatedException
     */
    private function checkMinOrderTotal($orderTotal)
    {
        if ($orderTotal < $this->minOrderTotal) {
            throw new OrderCantBeCreatedException('Order total price is less than min allowed');
        }
    }

    /**
     * @param string $country
     * @throws NonUniqueResultException
     * @throws OrderCantBeCreatedException
     */
    private function checkOrderLimitPerCountry($country)
    {
        $dateTimeSince = (new DateTime())->modify($this->orderLimitForCountryTimeFrame);
        $orderCount = $this->orderRepository->getOrderCountByCountrySinceDateTime($country, $dateTimeSince);

        if ($orderCount > $this->orderLimitForCountryPerTimeFrame) {
            throw new OrderCantBeCreatedException('Order limit from country per time frame exceeded');
        }
    }
}