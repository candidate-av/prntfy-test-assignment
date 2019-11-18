<?php

namespace App\Controller;

use App\Exception\ProductCantBeCreatedException;
use App\Service\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/products", name="add_product", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function addProduct(Request $request, ProductService $productService)
    {
        $json = $request->getContent();
        $requestData = json_decode($json, true);

        if (empty($requestData)) {
            throw new BadRequestHttpException('Bad request');
        }

        try {
            $newProduct = $productService->addProduct(
                $requestData['price'] ?? null,
                $requestData['colorCode'] ?? null,
                $requestData['sizeCode'] ?? null,
                $requestData['typeCode'] ?? null
            );
        } catch (\Exception $exception) {
            $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;

            if ($exception instanceof ProductCantBeCreatedException) {
                $statusCode = Response::HTTP_BAD_REQUEST;
            }

            return $this->json(
                ['error' => true, 'message' => $exception->getMessage(), 'code' => $exception->getCode()],
                $statusCode
            );
        }

        return $this->json([
            'error' => false,
            'data' => $newProduct->toArray()
        ]);
    }
}
