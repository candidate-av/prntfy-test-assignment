<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 * @ORM\Table(
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="product_unique",
 *            columns={"type_id", "size_id", "color_id"})
 *    }
 * )
 */
class Product
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="decimal", precision=13, scale=2)
     */
    private $price;

    /**
     * @var ProductType
     * @ORM\ManyToOne(targetEntity="App\Entity\ProductType")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

    /**
     * @var ProductColor
     * @ORM\ManyToOne(targetEntity="App\Entity\ProductColor")
     * @ORM\JoinColumn(nullable=false)
     */
    private $color;

    /**
     * @var ProductSize
     * @ORM\ManyToOne(targetEntity="App\Entity\ProductSize")
     * @ORM\JoinColumn(nullable=false)
     */
    private $size;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getPrice(): ?string
    {
        return $this->price;
    }

    /**
     * @param string $price
     * @return $this
     */
    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return ProductType|null
     */
    public function getType(): ?ProductType
    {
        return $this->type;
    }

    /**
     * @param ProductType|null $type
     * @return $this
     */
    public function setType(?ProductType $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return ProductColor|null
     */
    public function getColor(): ?ProductColor
    {
        return $this->color;
    }

    /**
     * @param ProductColor|null $color
     * @return $this
     */
    public function setColor(?ProductColor $color): self
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return ProductSize|null
     */
    public function getSize(): ?ProductSize
    {
        return $this->size;
    }

    /**
     * @param ProductSize|null $size
     * @return $this
     */
    public function setSize(?ProductSize $size): self
    {
        $this->size = $size;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'color' => $this->color ? $this->color->toArray() : null,
            'size' => $this->size ? $this->size->toArray() : null,
            'type' => $this->size ? $this->type->toArray() : null,
            'price' => floatval($this->price),
        ];
    }
}
