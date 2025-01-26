<?php

namespace App\Tests\Entity;

use App\Entity\Order;
use App\Entity\Product;
use App\Entity\OrderProduct;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{

  /**
   * Testa i metodi di get e set dell'entità Order
  */
  public function testOrderSettersAndGetters()
  {
    $order = new Order();

    $order->setName('Order A');
    $order->setDescription('Description of Order A');
    $order->setDate(new \DateTime('2025-01-01'));

    // Verifica che i getter restituiscano i valori giusti
    $this->assertEquals('Order A', $order->getName());
    $this->assertEquals('Description of Order A', $order->getDescription());
    $this->assertEquals(new \DateTime('2025-01-01'), $order->getDate());
  }

  /**
   * Testa il metodo per aggiungere un prodotto a un ordine
  */
  public function testOrderAddOrderProduct()
  {
    $order = new Order();
    $product = new Product();
    $product->setName('Product A')
            ->setPrice(100)
            ->setStock(10);
    
    $orderProduct = new OrderProduct();
    $orderProduct->setOrder($order)
                  ->setProduct($product)
                  ->setQuantity(2);

    $order->addOrderProduct($orderProduct);

    $this->assertCount(1, $order->getOrderProducts());
    $this->assertTrue($order->getOrderProducts()->contains($orderProduct));
  }

  /**
   * Testa il metodo per rimuovere un prodotto da un ordine
  */
  public function testOrderRemoveOrderProduct()
  {

    $order = new Order();
    $product = new Product();
    $product->setName('Product A')
            ->setPrice(100)
            ->setStock(10);
    
    $orderProduct = new OrderProduct();
    $orderProduct->setOrder($order)
                  ->setProduct($product)
                  ->setQuantity(2);

    
    $order->addOrderProduct($orderProduct);
    $order->removeOrderProduct($orderProduct);

    $this->assertCount(0, $order->getOrderProducts());
    $this->assertFalse($order->getOrderProducts()->contains($orderProduct));
  }
}
