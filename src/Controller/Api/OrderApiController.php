<?php

namespace App\Controller\Api;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class OrderApiController extends AbstractController
{
    #[Route('/api/orders', name: 'api_order_index', methods: ['GET'])]
    public function index(EntityManagerInterface $em): JsonResponse
    {
        // Recupera gli ordini dal database
        $orders = $em->getRepository(Order::class)->findAll();

        // Restituisci gli ordini come JSON
        return $this->json($orders);
    }
}