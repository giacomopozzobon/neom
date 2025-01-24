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

  
  /** Associations */


  #[ORM\ManyToMany(targetEntity: Order::class, mappedBy: 'products')]
  private Collection $orders;

  public function __construct()
  {
      $this->orders = new ArrayCollection();
  }

  /**
   * Metodo per ottenere gli ordini associati al prodotto.
   *
   * @return Collection
   */
  public function getOrders(): Collection
  {
      return $this->orders;
  }

  /**
   * Metodo per aggiungere un ordine al prodotto.
   *
   * @param Order $order
   * @return static
   */
  public function addOrder(Order $order): self
  {
      if (!$this->orders->contains($order)) {
          $this->orders->add($order);
      }

      return $this;
  }

  /**
   * Metodo per rimuovere un ordine dal prodotto.
   *
   * @param Order $order
   * @return static
   */
  public function removeOrder(Order $order): self
  {
      $this->orders->removeElement($order);

      return $this;
  }
}
