<?php

namespace App\Service;


use App\Entity\Product;
use App\Entity\ProductColor;
use App\Entity\ProductSize;
use App\Entity\ProductType;
use App\Exception\ProductCantBeCreatedException;
use App\Repository\ProductColorRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductSizeRepository;
use App\Repository\ProductTypeRepository;
use Doctrine\Common\Persistence\ObjectManager;

class ProductService
{
    /**
     * @var ProductColorRepository
     */
    private $productColorRepository;
    /**
     * @var ProductSizeRepository
     */
    private $productSizeRepository;
    /**
     * @var ProductTypeRepository
     */
    private $productTypeRepository;
    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * ProductService constructor.
     * @param ProductColorRepository $productColorRepository
     * @param ProductSizeRepository $productSizeRepository
     * @param ProductTypeRepository $productTypeRepository
     * @param ProductRepository $productRepository
     * @param ObjectManager $objectManager
     */
    public function __construct(
        ProductColorRepository $productColorRepository,
        ProductSizeRepository $productSizeRepository,
        ProductTypeRepository $productTypeRepository,
        ProductRepository $productRepository,
        ObjectManager $objectManager
    )
    {
        $this->productColorRepository = $productColorRepository;
        $this->productSizeRepository = $productSizeRepository;
        $this->productTypeRepository = $productTypeRepository;
        $this->productRepository = $productRepository;
        $this->objectManager = $objectManager;
    }

    /**
     * @param float $price
     * @param string $colorCode
     * @param string $sizeCode
     * @param string $typeCode
     *
     * @return Product
     * @throws ProductCantBeCreatedException
     */
    public function addProduct($price, $colorCode, $sizeCode, $typeCode)
    {
        $productColor = $this->getProductColor($colorCode);
        $productSize = $this->getProductSize($sizeCode);
        $productType = $this->getProductType($typeCode);

        $this->checkProductExists($productColor, $productSize, $productType);

        $newProduct = (new Product())
            ->setPrice($price)
            ->setColor($productColor)
            ->setSize($productSize)
            ->setType($productType);

        $this->objectManager->persist($newProduct);
        $this->objectManager->flush();

        return $newProduct;
    }

    /**
     * @param string $colorCode
     * @return ProductColor
     * @throws ProductCantBeCreatedException
     */
    private function getProductColor($colorCode): ProductColor
    {
        $productColor = $this->productColorRepository->findOneBy(['code' => $colorCode]);

        if (empty($productColor)) {
            throw new ProductCantBeCreatedException(ProductCantBeCreatedException::MESSAGE_PRODUCT_COLOR_DOES_NOT_EXISTS);
        }

        return $productColor;
    }

    /**
     * @param string $sizeCode
     * @return ProductSize
     * @throws ProductCantBeCreatedException
     */
    private function getProductSize($sizeCode): ProductSize
    {
        $productSize = $this->productSizeRepository->findOneBy(['code' => $sizeCode]);

        if (empty($productSize)) {
            throw new ProductCantBeCreatedException(ProductCantBeCreatedException::MESSAGE_PRODUCT_SIZE_DOES_NOT_EXISTS);
        }
        return $productSize;
    }

    /**
     * @param string $typeCode
     * @return ProductType
     * @throws ProductCantBeCreatedException
     */
    private function getProductType($typeCode): ProductType
    {
        $productType = $this->productTypeRepository->findOneBy(['code' => $typeCode]);

        if (empty($productType)) {
            throw new ProductCantBeCreatedException(ProductCantBeCreatedException::MESSAGE_PRODUCT_TYPE_DOES_NOT_EXISTS);
        }

        return $productType;
    }

    /**
     * @param ProductColor $productColor
     * @param ProductSize $productSize
     * @param ProductType $productType
     * @throws ProductCantBeCreatedException
     */
    private function checkProductExists(ProductColor $productColor, ProductSize $productSize, ProductType $productType)
    {
        $existingProduct = $this->productRepository->findOneBy(
            [
                'color' => $productColor,
                'size' => $productSize,
                'type' => $productType
            ]
        );

        if (!empty($existingProduct)) {
            throw new ProductCantBeCreatedException(ProductCantBeCreatedException::MESSAGE_PRODUCT_EXISTS);
        }
    }
}