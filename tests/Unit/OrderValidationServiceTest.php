<?php

namespace App\Tests\Unit;

use App\Entity\Order;
use App\Exception\OrderCantBeCreatedException;
use App\Repository\OrderRepository;
use App\Service\OrderValidationService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class OrderValidationServiceTest extends TestCase
{
    const MIN_ORDER_TOTAL = 10;
    const ORDER_LIMIT_PER_TIME_FRAME = 20;
    const ORDER_LIMIT_TIME_FRAME = '-10 seconds';

    /**
     * @var MockObject|OrderRepository
     */
    private $orderRepositoryMock;

    /**
     * @var OrderValidationService
     */
    private $orderValidationService;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->orderRepositoryMock = $this->getMockBuilder(OrderRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['getOrderCountByCountrySinceDateTime'])
            ->getMock();

        $this->orderValidationService = new OrderValidationService(
            self::MIN_ORDER_TOTAL,
            self::ORDER_LIMIT_PER_TIME_FRAME,
            self::ORDER_LIMIT_TIME_FRAME,
            $this->orderRepositoryMock
        );
    }

    /**
     * @param float $orderTotalPrice
     * @param bool $exceptionExpected
     * @dataProvider checkOrderCanBePlacedDataProvider
     */
    public function testCheckOrderCanBePlaced($orderTotalPrice, $ordersInTimeFrame, $exceptionExpected)
    {
        $this->orderRepositoryMock->expects($this->atMost(1))
            ->method('getOrderCountByCountrySinceDateTime')
            ->willReturn($ordersInTimeFrame);

        $order = $this->getOrderMock($orderTotalPrice);

        if ($exceptionExpected) {
            $this->expectException(OrderCantBeCreatedException::class);
        }

        $this->orderValidationService->checkOrderCanBePlaced($order);
    }

    /**
     * Create Order Mock
     *
     * @param float $totalPrice
     * @param string $country
     * @return MockObject|Order
     */
    private function getOrderMock($totalPrice, $country = 'US')
    {
        $orderMock = $this->getMockBuilder(Order::class)
            ->setMethods(['getCountry', 'getTotalPrice'])
            ->getMock();

        $orderMock->expects($this->atMost(1))->method('getCountry')->willReturn($country);
        $orderMock->expects($this->atMost(1))->method('getTotalPrice')->willReturn($totalPrice);

        return $orderMock;

    }

    /**
     * Test cases
     *
     * @return array
     */
    public function checkOrderCanBePlacedDataProvider()
    {
        return [
            [   // test order total less than min allowed order total
                'orderTotalPrice' => self::MIN_ORDER_TOTAL - 1,
                'ordersInTimeFrame' => self::ORDER_LIMIT_PER_TIME_FRAME - 1,
                'exceptionExpected' => true
            ],
            [   // test order total equals to min allowed order total
                'orderTotalPrice' => self::MIN_ORDER_TOTAL,
                'ordersInTimeFrame' => self::ORDER_LIMIT_PER_TIME_FRAME - 1,
                'exceptionExpected' => false
            ],
            [   // test order total greater than min allowed order total
                // orders count in time frame less than limit
                'orderTotalPrice' => self::MIN_ORDER_TOTAL + 1,
                'ordersInTimeFrame' => self::ORDER_LIMIT_PER_TIME_FRAME - 1,
                'exceptionExpected' => false
            ],
            [   // and orders count in time frame equals to limit
                'orderTotalPrice' => self::MIN_ORDER_TOTAL + 1,
                'ordersInTimeFrame' => self::ORDER_LIMIT_PER_TIME_FRAME,
                'exceptionExpected' => true
            ],
            [   // and orders count in time frame greater than limit
                'orderTotalPrice' => self::MIN_ORDER_TOTAL + 1,
                'ordersInTimeFrame' => self::ORDER_LIMIT_PER_TIME_FRAME + 10,
                'exceptionExpected' => true
            ],

        ];
    }
}
