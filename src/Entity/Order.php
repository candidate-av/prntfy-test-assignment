<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderRepository")
 * @ORM\Table(
 *     name="`order`",
 *     indexes={
 *          @ORM\Index(name="created_at_idx", columns={"created_at"}),
 *          @ORM\Index(name="country_idx", columns={"country"})
 *    }
 * )
 */
class Order
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=2)
     */
    private $country;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var OrderProduct[]
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\OrderProduct",
     *     mappedBy="productOrder",
     *     orphanRemoval=true,
     *     cascade={"persist"},
     *     fetch="EAGER"
     * )
     */
    private $orderProducts;

    public function __construct()
    {
        $this->orderProducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function addOrderProduct(OrderProduct $orderProduct): self
    {
        if (!$this->orderProducts->contains($orderProduct)) {
            $this->orderProducts[] = $orderProduct;
            $orderProduct->setProductOrder($this);
        }

        return $this;
    }

    public function removeOrderProduct(OrderProduct $orderProduct): self
    {
        if ($this->orderProducts->contains($orderProduct)) {
            $this->orderProducts->removeElement($orderProduct);
            // set the owning side to null (unless already changed)
            if ($orderProduct->getProductOrder() === $this) {
                $orderProduct->setProductOrder(null);
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $orderProducts = [];

        foreach ($this->orderProducts as $orderProduct) {
            $orderProducts[] = $orderProduct->toArray();
        }

        return [
            'id' => $this->id,
            'country' => $this->country,
            'created' => $this->createdAt->format("Y-m-d H:i:s"),
            'orderProducts' => $orderProducts,
            'totalPrice' => $this->getTotalPrice(),
        ];
    }

    /**
     *  Get total price
     * @return float
     */
    public function getTotalPrice()
    {
        $totalPrice = 0.00;

        foreach ($this->getOrderProducts() as $orderProduct) {
            $totalPrice += ($orderProduct->getQuantity() * $orderProduct->getProduct()->getPrice());
        }

        return $totalPrice;
    }

    /**
     * @return Collection|OrderProduct[]
     */
    public function getOrderProducts(): Collection
    {
        return $this->orderProducts;
    }
}
