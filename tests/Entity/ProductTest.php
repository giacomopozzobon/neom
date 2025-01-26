<?php

namespace App\Tests\Entity;

use App\Entity\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    /**
     * Testa i metodi di get e set dell'entità Product
     */
    public function testProductSettersAndGetters()
    {
      $product = new Product();

      $product->setName('Product A');
      $product->setPrice(100.50);
      $product->setStock(20);

      $this->assertEquals('Product A', $product->getName());
      $this->assertEquals(100.50, $product->getPrice());
      $this->assertEquals(20, $product->getStock());
    }
}
