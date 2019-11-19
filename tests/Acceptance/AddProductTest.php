<?php

namespace App\Tests\Acceptance;

use App\Entity\ProductColor;
use App\Entity\ProductSize;
use App\Entity\ProductType;
use Symfony\Component\HttpFoundation\Response;

class AddProductTest extends AbstractAcceptance
{
    /**
     * Test that product can be added
     */
    public function testAddProduct()
    {
        $client = static::createClient();
        $uri = $client->getContainer()->get('router')->generate('add_product');

        $productColor = $this->createProductAttribute(ProductColor::class, $this->uniqueKey('color'));
        $productSize = $this->createProductAttribute(ProductSize::class, $this->uniqueKey('size'));
        $productType = $this->createProductAttribute(ProductType::class, $this->uniqueKey('type'));

        $data = [
            'price' => 9.15,
            'colorCode' => $productColor->getCode(),
            'sizeCode' => $productSize->getCode(),
            'typeCode' => $productType->getCode(),
        ];

        $client->request('POST', $uri, [], [], [], json_encode($data));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseContent = json_decode($client->getResponse()->getContent(), true);
        $this->assertNotEmpty($responseContent);
        $this->assertFalse($responseContent['error']);
        $this->assertEquals($data['price'], $responseContent['data']['price']);
        $this->assertEquals($data['colorCode'], $responseContent['data']['color']['code']);
        $this->assertEquals($data['sizeCode'], $responseContent['data']['size']['code']);
        $this->assertEquals($data['typeCode'], $responseContent['data']['type']['code']);
    }
}
