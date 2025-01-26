<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Classe che rappresenta un prodotto.
 */
#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Table(name: '`products`')]


class Product
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  private ?string $name = null;

  #[ORM\Column]
  private ?float $price = null;

  #[ORM\Column(type: 'integer', options: ['default' => 0])]
  private ?int $stock = null;


  /** Getters & Setters */


  public function getId(): ?int
  {
    return $this->id;
  }

  public function getName(): ?string
  {
    return $this->name;
  }

  public function getPrice(): ?float
  {
    return $this->price;
  }

  public function getStock(): ?int
  {
    return $this->stock;
  }

  public function setName(string $name): static
  {
    $this->name = $name;

    return $this;
  }

  public function setPrice(float $price): static
  {
    $this->price = $price;

    return $this;
  }

  public function setStock(int $stock): static
  {
    $this->stock = $stock;

    return $this;
  }

  
  /** Associations */


  #[ORM\OneToMany(mappedBy: 'product', targetEntity: OrderProduct::class)]
  private Collection $orderProducts;

  public function __construct()
  {
    $this->orderProducts = new ArrayCollection();
  }

  public function getOrderProducts(): Collection
  {
    return $this->orderProducts;
  }

  public function addOrderProduct(OrderProduct $orderProduct): self
  {
    if (!$this->orderProducts->contains($orderProduct)) {
      $this->orderProducts->add($orderProduct);
      $orderProduct->setProduct($this);
    }

    return $this;
  }

  public function removeOrderProduct(OrderProduct $orderProduct): self
  {
    if ($this->orderProducts->removeElement($orderProduct)) {
      if ($orderProduct->getProduct() === $this) {
        $orderProduct->setProduct(null);
      }
    }

    return $this;
  }
}
