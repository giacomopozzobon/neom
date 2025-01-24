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


  #[ORM\ManyToMany(targetEntity: Product::class, inversedBy: 'orders')]
  private Collection $products;

  public function __construct()
  {
    $this->products = new ArrayCollection();
  }

  /**
   * Metodo per ottenere i prodotti associati all'ordine.
   *
   * @return Collection
   */
  public function getProducts(): Collection
  {
    return $this->products;
  }

  /**
   * Metodo per aggiungere un prodotto all'ordine.
   *
   * @param Product $product
   * @return static
   */
  public function addProduct(Product $product): self
  {
    if (!$this->products->contains($product)) {
      $this->products->add($product);
      $product->addOrder($this);  // Aggiunge l'ordine al prodotto (relazione inversa)
    }

    return $this;
  }

  /**
   * Metodo per rimuovere un prodotto dall'ordine.
   *
   * @param Product $product
   * @return static
   */
  public function removeProduct(Product $product): self
  {
    if ($this->products->removeElement($product)) {
      $product->removeOrder($this);  // Rimuove l'ordine dal prodotto (relazione inversa)
    }

    return $this;
  }
}