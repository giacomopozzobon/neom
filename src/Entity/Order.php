<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Classe che rappresenta un ordine.
 */
#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]

class Order
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  private ?string $name = null;

  #[ORM\Column(length: 255, nullable: true)]
  private ?string $description = null;

  #[ORM\Column(type: Types::DATETIME_MUTABLE)]
  private ?\DateTimeInterface $date = null;


  /** Getters & Setters */


  public function getId(): ?int
  {
    return $this->id;
  }

  public function getName(): ?string
  {
    return $this->name;
  }

  public function getDescription(): ?string
  {
    return $this->description;
  }

  public function getDate(): ?\DateTimeInterface
  {
    return $this->date;
  }

  public function setName(string $name): static
  {
    $this->name = $name;
    return $this;
  }

  public function setDescription(?string $description): static
  {
    $this->description = $description;
    return $this;
  }
  
  public function setDate(\DateTimeInterface $date): static
  {
    $this->date = $date;
    return $this;
  }




  /** Associations */


  #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderProduct::class, cascade: ['persist', 'remove'])]
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
      $orderProduct->setOrder($this);
    }

    return $this;
  }

  public function removeOrderProduct(OrderProduct $orderProduct): self
  {
    if ($this->orderProducts->removeElement($orderProduct)) {
      if ($orderProduct->getOrder() === $this) {
        $orderProduct->setOrder(null);
      }
    }

    return $this;
  }
}