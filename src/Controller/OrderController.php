<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OrderController extends AbstractController
{
  #[Route('/order', name: 'order_index')]
  public function index(EntityManagerInterface $em): Response
  {
    $orders = $em->getRepository(Order::class)->findAll();

    return $this->render('order/index.html.twig', [
      'orders' => $orders,
    ]);
  }
}
