<?php

namespace App\Tests\Unit;

use App\Entity\Product;
use App\Entity\ProductColor;
use App\Entity\ProductSize;
use App\Entity\ProductType;
use App\Exception\ProductCantBeCreatedException;
use App\Repository\ProductColorRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductSizeRepository;
use App\Repository\ProductTypeRepository;
use App\Service\ProductService;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ProductServiceTest extends TestCase
{
    /**
     * @var MockObject|ProductColorRepository
     */
    private $productColorRepositoryMock;
    /**
     * @var MockObject|ProductSizeRepository
     */
    private $productSizeRepositoryMock;
    /**
     * @var MockObject|ProductTypeRepository
     */
    private $productTypeRepositoryMock;
    /**
     * @var MockObject|ObjectManager
     */
    private $objectManagerMock;

    /**
     * @var MockObject|ProductRepository
     */
    private $productRepository;

    /**
     * @var ProductService
     */
    private $productService;

    public function setUp()
    {
        $this->productColorRepositoryMock = $this->getMockBuilder(ProductColorRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['findOneBy'])
            ->getMock();

        $this->productSizeRepositoryMock = $this->getMockBuilder(ProductSizeRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['findOneBy'])
            ->getMock();

        $this->productTypeRepositoryMock = $this->getMockBuilder(ProductTypeRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['findOneBy'])
            ->getMock();

        $this->productRepository = $this->getMockBuilder(ProductRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['findOneBy'])
            ->getMock();

        $this->objectManagerMock = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['persist', 'flush'])
            ->getMock();

        $this->productService = new ProductService(
            $this->productColorRepositoryMock,
            $this->productSizeRepositoryMock,
            $this->productTypeRepositoryMock,
            $this->productRepository,
            $this->objectManagerMock
        );
    }

    /**
     * @dataProvider addProductDataProvider
     *
     * @param $colorExists
     * @param $sizeExists
     * @param $typeExists
     * @param $sameProductExists
     * @param $exceptionExpected
     * @throws ProductCantBeCreatedException
     *
     */
    public function testAddProduct($colorExists, $sizeExists, $typeExists, $sameProductExists, $exceptionExpected)
    {
        $this->productColorRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn($colorExists ? new ProductColor() : null);

        $this->productSizeRepositoryMock
            ->expects($colorExists ? $this->once() : $this->never())
            ->method('findOneBy')
            ->willReturn($sizeExists ? new ProductSize() : null);

        $this->productTypeRepositoryMock
                ->expects($colorExists && $sizeExists ? $this->once() : $this->never())
                ->method('findOneBy')
                ->willReturn($typeExists ? new ProductType() : null);

        $invocations = $colorExists && $sizeExists && $typeExists ? $this->once() : $this->never();
        $this->productRepository
            ->expects($invocations)
            ->method('findOneBy')
            ->willReturn($sameProductExists ? new Product() : null);

        if ($exceptionExpected) {
            $this->expectException(ProductCantBeCreatedException::class);
            $this->expectExceptionMessage($exceptionExpected);
        } else {
            $this->objectManagerMock->expects($this->once())->method('persist');
            $this->objectManagerMock->expects($this->once())->method('flush');
        }

        $newProduct = $this->productService->addProduct(10.00, 'rd', 'xl', 'mg');

        $this->assertInstanceOf(Product::class, $newProduct);
    }

    /**
     * @return array
     */
    public function addProductDataProvider()
    {
        return [
            [
                'colorExists' => true,
                'sizeExists' => true,
                'typeExists' => true,
                'sameProductExists' => false,
                'exceptionExpected' => false,
            ],
            [
                'colorExists' => false,
                'sizeExists' => true,
                'typeExists' => true,
                'sameProductExists' => false,
                'exceptionExpected' => ProductCantBeCreatedException::MESSAGE_PRODUCT_COLOR_DOES_NOT_EXISTS,
            ],
            [
                'colorExists' => true,
                'sizeExists' => false,
                'typeExists' => false,
                'sameProductExists' => false,
                'exceptionExpected' => ProductCantBeCreatedException::MESSAGE_PRODUCT_SIZE_DOES_NOT_EXISTS,
            ],
            [
                'colorExists' => true,
                'sizeExists' => true,
                'typeExists' => false,
                'sameProductExists' => false,
                'exceptionExpected' => ProductCantBeCreatedException::MESSAGE_PRODUCT_TYPE_DOES_NOT_EXISTS,
            ],
            [
                'colorExists' => true,
                'sizeExists' => true,
                'typeExists' => true,
                'sameProductExists' => true,
                'exceptionExpected' => ProductCantBeCreatedException::MESSAGE_PRODUCT_EXISTS,
            ],
        ];
    }

}
