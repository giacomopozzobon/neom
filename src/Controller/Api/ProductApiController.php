<?php
namespace App\Controller\Api;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ProductApiController extends AbstractController
{
  /**
   *
   * Retrieves a list of all products from the database.
   *
   * Example request:
   * GET /api/products
   *
   * Example response:
   * [
   *     {
   *         "id": 1,
   *         "name": "Product 1",
   *         "price": 10.99,
   *         "stock": 50
   *     },
   *     ...
   * ]
   */
  #[Route('/api/products', name: 'api_product_index', methods: ['GET'])]
  public function index(EntityManagerInterface $em): JsonResponse
  {
      $products = $em->getRepository(Product::class)->findAll();
      
      
      $data = [];
      foreach ($products as $product) {
        $data[] = [
          'id' => $product->getId(),
          'name' => $product->getName(),
          'price' => $product->getPrice(),
          'stock' => $product->getStock()
        ];
      }

      // Restituisci i dati dei prodotti senza relazioni
      return $this->json($data);
  }

  /**
   *
   * Retrieves a single product from the database.
   *
   * Example request:
   * GET /api/products/1
   *
   * Example response:
   *
   *  {
   *      "id": 1,
   *      "name": "Product 1",
   *      "price": 10.99,
   *      "stock": 50
   *  }
   */
  #[Route('/api/products/{id}', name: 'api_product_show', methods: ['GET'])]
  public function show(int $id, EntityManagerInterface $em): JsonResponse
  {
    $product = $em->getRepository(Product::class)->find($id);

    if (!$product) { return new JsonResponse(['error' => 'Product not found'], JsonResponse::HTTP_NOT_FOUND); }

    return $this->json($product);
  }

  /**
   *
   * Creates a new product in the database. The request body must include
   * the `name` and `price` fields. The `stock` field is optional and defaults to 0.
   *
   *
   * Example request:
   * POST /api/products
   * {
   *     "name": "New Product",
   *     "price": 19.99,
   *     "stock": 100
   * }
   *
   * Example response (on success):
   * {
   *     "id": 3,
   *     "name": "New Product",
   *     "price": 19.99,
   *     "stock": 100
   * }
   *
   */
  #[Route('/api/products', name: 'api_product_create', methods: ['POST'])]
  public function create(Request $request, EntityManagerInterface $em): JsonResponse
  {
    $data = json_decode($request->getContent(), true);

    // Validate required fields
    if (empty($data['name']) || empty($data['price'])) {
      return new JsonResponse(['error' => 'Name and price are required'], JsonResponse::HTTP_BAD_REQUEST);
    }

    // Create new product
    $product = new Product();
    $product->setName($data['name']);
    $product->setPrice($data['price']);
    $product->setStock($data['stock'] ?? 0);  // Default to 0 if stock is not provided

    $em->persist($product);
    $em->flush();

    return new JsonResponse(['message' => 'Product created successfully!'], JsonResponse::HTTP_CREATED);
  }

  /**
   *
   * Deletes an existing product by its ID. If the product is not found,
   * a 404 error response will be returned.
   *
   *
   * Example request:
   * DELETE /api/products/1
   *
   * Example response (on success):
   * {
   *     "message": "Product deleted successfully"
   * }
   *
   * Example response (on failure):
   * {
   *     "error": "Product not found"
   * }
   */
  #[Route('/api/products/{id}', name: 'api_product_delete', methods: ['DELETE'])]
  public function delete(int $id, EntityManagerInterface $em): JsonResponse
  {
    $product = $em->getRepository(Product::class)->find($id);

    if (!$product) {
      return new JsonResponse(['error' => 'Product not found'], JsonResponse::HTTP_NOT_FOUND);
    }

    $em->remove($product);
    $em->flush();

    return new JsonResponse(['message' => 'Product deleted successfully'], JsonResponse::HTTP_OK);
  }

  /**
   *
   * Updates an existing product by its ID. The request body can include
   * the `name`, `price`, and/or `stock` fields. Only the fields provided
   * in the request will be updated.
   *
   * Example request:
   * PUT /api/products/1
   * {
   *     "name": "Updated Product",
   *     "price": 29.99
   * }
   *
   * Example response (on success):
   * {
   *     "id": 1,
   *     "name": "Updated Product",
   *     "price": 29.99,
   *     "stock": 50
   * }
   *
   * Example response (on failure):
   * {
   *     "error": "Product not found"
   * }
   */
  #[Route('/api/products/{id}', name: 'api_product_update', methods: ['PUT'])]
  public function update(int $id, Request $request, EntityManagerInterface $em, ValidatorInterface $validator): JsonResponse
  {
    $product = $em->getRepository(Product::class)->find($id);

    if (!$product) { return new JsonResponse(['error' => 'Product not found'], JsonResponse::HTTP_NOT_FOUND); }

    $data = json_decode($request->getContent(), true);

    // Update fields if provided in the request
    $updatedFields = ['name', 'price', 'stock'];
    foreach ($updatedFields as $field) {
      if (isset($data[$field])) {
        $setter = 'set' . ucfirst($field);
        $product->$setter($data[$field]);
      }
    }

    $errors = $validator->validate($product);
    if (count($errors) > 0) {
      return new JsonResponse((string) $errors, JsonResponse::HTTP_BAD_REQUEST);
    }

    $em->flush();

    return new JsonResponse($product, JsonResponse::HTTP_OK);
  }
}
