<?php

namespace App\Controller\Api;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProductApiController extends AbstractController
{
  #[Route('/api/products', name: 'api_product_index', methods: ['GET'])]
  public function index(EntityManagerInterface $em): JsonResponse
  {
    // Recupera i prodotti dal database
    $products = $em->getRepository(Product::class)->findAll();

    // Restituisce i prodotti come JSON
    return $this->json($products);
  }

  #[Route('/api/products', name: 'api_product_create', methods: ['POST'])]
  public function create(Request $request, EntityManagerInterface $em): JsonResponse
  {
    $data = json_decode($request->getContent(), true);

    if (empty($data['name']) || empty($data['price'])) {
      return new JsonResponse(['error' => 'Name and price are required'], JsonResponse::HTTP_BAD_REQUEST);
    }

    // Crea un nuovo prodotto
    $product = new Product();
    $product->setName($data['name']);
    $product->setPrice($data['price']);

    // Salva il prodotto nel database
    $em->persist($product);
    $em->flush();

    // Restituisce il prodotto creato con un codice di stato 201
    return new JsonResponse($product, JsonResponse::HTTP_CREATED);
  }

  #[Route('/api/products/{id}', name: 'api_product_delete', methods: ['DELETE'])]
  public function delete(int $id, EntityManagerInterface $em): JsonResponse
  {
    // Recupera il prodotto dal database
    $product = $em->getRepository(Product::class)->find($id);

    if (!$product) {
      return new JsonResponse(['error' => 'Product not found'], JsonResponse::HTTP_NOT_FOUND);
    }

    // Elimina il prodotto dal database
    $em->remove($product);
    $em->flush();

    // Restituisce un messaggio di successo
    return new JsonResponse(['message' => 'Product deleted successfully'], JsonResponse::HTTP_OK);
  }
}
