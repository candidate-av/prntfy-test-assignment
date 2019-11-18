<?php

namespace App\Controller;

use App\Entity\Order;
use App\Exception\InvalidArgumentException;
use App\Repository\OrderRepository;
use App\Repository\ProductTypeRepository;
use App\Service\OrderService;
use App\Service\OrderValidationService;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\DBAL\DBALException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    /**
     * @Route("/orders", name="add_order", methods={"POST"})
     * @param Request $request
     * @param OrderService $orderService
     * @param ObjectManager $objectManager
     * @param OrderValidationService $orderValidationService
     * @return JsonResponse
     * @throws Exception
     */
    public function addOrder(
        Request $request,
        OrderService $orderService,
        ObjectManager $objectManager,
        OrderValidationService $orderValidationService
    )
    {
        $json = $request->getContent();
        $requestData = json_decode($json, true);


        try {
            if (empty($requestData) || empty($requestData['products'])) {
                throw new InvalidArgumentException('Request does not contains product section');
            }

            $order = $orderService->createOrder($requestData['products'], $clientIp = $request->getClientIp());
            $orderValidationService->checkOrderCanBePlaced($order);

            $objectManager->persist($order);
            $objectManager->flush();
        } catch (Exception $e) {
            return $this->json([
                'error' => true,
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
        }

        return $this->json([
            'error' => false,
            'data' => $order->toArray()
        ]);
    }


    /**
     * @Route("/orders", name="get_orders", methods={"GET"})
     * @throws InvalidArgumentException
     * @throws DBALException
     */
    public function getOrders(Request $request, OrderRepository $orderRepository, ProductTypeRepository $productTypeRepository)
    {
        if (!empty($request->query->get('typeCode'))) {
            $productType = $productTypeRepository->findOneBy(['code' => $request->query->get('typeCode')]);

            if (empty($productType)) {
                throw new InvalidArgumentException('Product type does not exists');
            }

            $orders = $orderRepository->getOrdersByProductType($productType);
        } else {
            $orders = $orderRepository->findAll();
        }

        return $this->json([
            'error' => false,
            'count' => count($orders),
            'data' => $this->prepareOrderListOutput($orders)
        ]);
    }

    /**
     * @param Order[] $orders
     * @return array
     */
    private function prepareOrderListOutput($orders)
    {
        $result = [];

        foreach ($orders as $order) {
            $result[] = $order->toArray();
        }

        return $result;
    }
}
