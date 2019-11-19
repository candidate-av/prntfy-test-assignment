<?php

namespace App\Tests\Acceptance;

use App\Entity\ProductColor;
use App\Entity\ProductSize;
use App\Entity\ProductType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractAcceptance extends WebTestCase
{

    /**
     * @var ObjectManager|object
     */
    protected $entityManager;

    protected function setUp()
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * Create product attribute
     *
     * @param string $class
     * @param string $code
     * @return ProductColor|ProductSize|ProductType
     */
    protected function createProductAttribute($class, $code)
    {
        /** @var ProductColor|ProductSize|ProductType $productAttribute */
        $productAttribute = new $class();
        $productAttribute->setCode($code);
        $productAttribute->setDescription('Test ' . $code);
        $this->entityManager->persist($productAttribute);
        $this->entityManager->flush();

        return $productAttribute;
    }

    /**
     * Generate random key
     *
     * @param string $prefix
     * @return string
     */
    protected function uniqueKey($prefix)
    {
        return $prefix . '-' . getmypid() . time() . rand(1, 9999);
    }
}
