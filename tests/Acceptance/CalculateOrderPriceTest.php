<?php

namespace App\Tests\Acceptance;

use App\Entity\Product;
use App\Entity\ProductColor;
use App\Entity\ProductSize;
use App\Entity\ProductType;
use Symfony\Component\HttpFoundation\Response;

class CalculateOrderPrice extends AbstractAcceptance
{
    /**
     * Test order price is calculated correctly
     */
    public function testCalculateOrderPrice()
    {
        $data = [
            'products' => [
                ['productId' => $this->createProduct(10.22)->getId(), 'quantity' => 1],
                ['productId' => $this->createProduct(5.31)->getId(), 'quantity' => 2],
            ]
        ];

        $expectedTotalPrice = 20.84;

        $client = static::createClient();
        $uri = $client->getContainer()->get('router')->generate('add_order');

        $client->request('POST', $uri, [], [], [], json_encode($data));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseContent = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals($expectedTotalPrice, $responseContent['data']['totalPrice']);
        $this->assertFalse($responseContent['error']);
    }

    /**
     * Create Product entity with price
     * @param float $price
     * @return Product
     */
    private function createProduct($price)
    {
        $color = $this->createProductAttribute(ProductColor::class,  $this->uniqueKey('color'));
        $size = $this->createProductAttribute(ProductSize::class,  $this->uniqueKey('size'));
        $type = $this->createProductAttribute(ProductType::class,  $this->uniqueKey('type'));

        $product = (new Product())
            ->setSize($size)
            ->setColor($color)
            ->setType($type)
            ->setPrice($price);

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $product;
    }
}
