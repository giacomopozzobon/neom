<?php

namespace App\Controller\Api;

use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class OrderApiController extends AbstractController
{

  /**
   * Retrieves a list of all orders.
   *
   * Example request:
   * GET /api/orders
   *
   * Example response:
   * [
   *     {
   *         "id": 1,
   *         "name": "Order 1",
   *         "date": "2025-01-26",
   *         "products": [
   *             {
   *                 "id": 1,
   *                 "name": "Product 1",
   *                 "quantity": 2
   *             },
   *             ...
   *         ]
   *     },
   *     ...
   * ]
   */
  #[Route('/api/orders', name: 'api_order_index', methods: ['GET'])]
  public function index(EntityManagerInterface $em): JsonResponse
  {
    $orders = $em->getRepository(Order::class)->findAll();
    $data = [];

    foreach ($orders as $order) {
      // Recupera i prodotti associati all'ordine (usando la relazione OrderProduct)
      $productsData = [];
      foreach ($order->getOrderProducts() as $orderProduct) {
        $productsData[] = [
          'id' => $orderProduct->getProduct()->getId(),
          'name' => $orderProduct->getProduct()->getName(),
          'quantity' => $orderProduct->getQuantity(),
        ];
      }

      // Aggiungi l'ordine e i prodotti all'array di risposta
      $data[] = [
        'id' => $order->getId(),
        'name' => $order->getName(),
        'date' => $order->getDate()->format('Y-m-d'),
        'products' => $productsData,
      ];
    }

    // Restituisci la risposta JSON con i dati formattati
    return $this->json($data);
  }


  /**
   * Creates a new order with associated products.
   *
   * Example request:
   * POST /api/orders
   * {
   *     "name": "Order 1",
   *     "description": "Order description",
   *     "date": "2025-01-26",
   *     "products": [
   *         {
   *             "id": 1,
   *             "quantity": 2
   *         },
   *         {
   *             "id": 2,
   *             "quantity": 1
   *         }
   *     ]
   * }
   *
   * Example response (on success):
   * {
   *     "id": 1,
   *     "name": "Order 1",
   *     "description": "Order description",
   *     "date": "2025-01-26",
   *     "products": [
   *         {
   *             "id": 1,
   *             "name": "Product 1",
   *             "quantity": 2
   *         },
   *         ...
   *     ]
   * }
   */
  #[Route('/api/orders', name: 'api_order_create', methods: ['POST'])]
  public function create(Request $request, EntityManagerInterface $em): JsonResponse
  {
    $data = json_decode($request->getContent(), true);

    // Validate required fields
    if (empty($data['name']) || empty($data['date'])) {
      return new JsonResponse(['error' => 'Name and date are required'], JsonResponse::HTTP_BAD_REQUEST);
    }

    // Start a transaction to ensure consistency
    $em->beginTransaction();

    try {
      // Create the new order
      $order = new Order();
      $order->setName($data['name']);
      $order->setDescription($data['description'] ?? '');
      $order->setDate(new \DateTime($data['date']));

      $em->persist($order);

      // Process each product in the order
      if (!empty($data['products'])) {
        foreach ($data['products'] as $productData) {
          $product = $em->getRepository(Product::class)->find($productData['id']);
          if (!$product) {
            return new JsonResponse(['error' => 'Product not found'], JsonResponse::HTTP_NOT_FOUND);
          }

          // Check stock availability
          if ($product->getStock() < $productData['quantity']) {
            return new JsonResponse(['error' => 'Not enough stock for product ' . $product->getName()], JsonResponse::HTTP_BAD_REQUEST);
          }

          // Create the OrderProduct
          $orderProduct = new OrderProduct();
          $orderProduct->setOrder($order);
          $orderProduct->setProduct($product);
          $orderProduct->setQuantity($productData['quantity']);

          // Subtract the ordered quantity from product stock
          $product->setStock($product->getStock() - $productData['quantity']);
          $em->persist($orderProduct);
        }
      }

      // Commit the transaction
      $em->flush();
      $em->commit();

      return new JsonResponse($order, JsonResponse::HTTP_CREATED);
    } catch (\Exception $e) {
      // If something goes wrong, rollback the transaction
      $em->rollback();
      return new JsonResponse(['error' => 'An error occurred: ' . $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  /**
   * Retrieves a single order by its ID.
   *
   * Example request:
   * GET /api/orders/1
   *
   * Example response:
   * {
   *     "id": 1,
   *     "name": "Order 1",
   *     "date": "2025-01-26",
   *     "products": [
   *         {
   *             "id": 1,
   *             "name": "Product 1",
   *             "quantity": 2
   *         },
   *         ...
   *     ]
   * }
   */
  #[Route('/api/orders/{id}', name: 'api_order_show', methods: ['GET'])]
  public function show(int $id, EntityManagerInterface $em): JsonResponse
  {
      // Recupera l'ordine tramite ID
      $order = $em->getRepository(Order::class)->find($id);

      // Se l'ordine non esiste, restituisci un errore
      if (!$order) {
        return new JsonResponse(['error' => 'Order not found'], JsonResponse::HTTP_NOT_FOUND);
      }

      // Crea un array per i prodotti dell'ordine
      $productsData = [];
      foreach ($order->getOrderProducts() as $orderProduct) {
        $productsData[] = [
          'id' => $orderProduct->getProduct()->getId(),
          'name' => $orderProduct->getProduct()->getName(),
          'quantity' => $orderProduct->getQuantity(),
        ];
      }

      // Prepara la risposta con i dati dell'ordine e i suoi prodotti
      $data = [
        'id' => $order->getId(),
        'name' => $order->getName(),
        'date' => $order->getDate()->format('Y-m-d'),
        'products' => $productsData,
      ];

      // Restituisci la risposta JSON con i dati formattati
      return $this->json($data);
  }
}