<?php

namespace App\DataFixtures;

use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Entity\Product;
use App\Entity\ProductAttribute;
use App\Entity\ProductColor;
use App\Entity\ProductSize;
use App\Entity\ProductType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $attributes = $this->createProductAttributes($manager);
        $products = $this->createProducts($manager, $attributes);
        $this->createOrders($manager, $products);


        $manager->flush();
    }

    private function createOrders(ObjectManager $manager, $products)
    {
        foreach ($products as $product) {
            $order = new Order();
            $order->addOrderProduct(new OrderProduct($product, rand(1, 5)))
                ->addOrderProduct(new OrderProduct($products[array_rand($products)], rand(1, 5)))
                ->setCountry('US');

            $manager->persist($order);
        }
    }

    /**
     * @param ObjectManager $manager
     * @param array $attributes
     * @return array
     */
    private function createProducts(ObjectManager $manager, $attributes):array
    {
        $products = [];

        for ($type = 0; $type <= 2; $type++) {
            for ($size = 0; $size <= 2; $size ++) {
                for ($color = 0; $color <= 2; $color ++) {
                    /** @var ProductColor $productColor */
                    $productColor = $attributes[ProductColor::class][$color];
                    /** @var ProductType $productType */
                    $productType = $attributes[ProductType::class][$type];
                    /** @var ProductSize $productSize */
                    $productSize = $attributes[ProductSize::class][$size];

                    $product = new Product();
                    $product->setType($productType)
                        ->setColor($productColor)
                        ->setSize($productSize)
                        ->setPrice((rand(5,20) + 0.99));

                    $manager->persist($product);
                    $products[] = $product;
                }
            }
        }

        return $products;
    }

    /**
     * @param ObjectManager $manager
     * @return array
     */
    private function createProductAttributes(ObjectManager $manager):array
    {
        $result = [];
        $productAttributesData = [
            ['id' => 1, 'code' => 'l', 'descr' => 'Large', 'class' => ProductSize::class],
            ['id' => 2, 'code' => 'm', 'descr' => 'Medium', 'class' => ProductSize::class],
            ['id' => 3, 'code' => 's', 'descr' => 'Small', 'class' => ProductSize::class],
            ['id' => 4, 'code' => 'xl', 'descr' => 'Extra Large', 'class' => ProductSize::class],

            ['id' => 1, 'code' => 'mg', 'descr' => 'Mug', 'class' => ProductType::class],
            ['id' => 2, 'code' => 'srt', 'descr' => 'Shirt', 'class' => ProductType::class],
            ['id' => 3, 'code' => 'ht', 'descr' => 'Hat', 'class' => ProductType::class],

            ['id' => 1, 'code' => 'rd', 'descr' => 'Red', 'class' => ProductColor::class],
            ['id' => 2, 'code' => 'gr', 'descr' => 'Green', 'class' => ProductColor::class],
            ['id' => 3, 'code' => 'bl', 'descr' => 'Blue', 'class' => ProductColor::class],
        ];

        foreach ($productAttributesData as $data) {
            /** @var ProductAttribute $attribute */
            $attribute = new $data['class']();
            $attribute
                ->setId($data['id'])
                ->setCode($data['code'])
                ->setDescription($data['descr']);

            $manager->persist($attribute);

            $result[$data['class']][] = $attribute;
        }

        return $result;
    }
}
