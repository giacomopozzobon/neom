<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Entity\Product;
use App\Form\OrderType;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

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



  #[Route('/order/create', name: 'order_create')]
  public function create(Request $request, EntityManagerInterface $em): Response
  {
    // Otteniamo tutti i prodotti disponibili
    $products = $em->getRepository(Product::class)->findAll();

    // Creiamo il form dell'ordine, passando i prodotti disponibili
    $order = new Order();
    $form = $this->createForm(OrderType::class, $order, ['products' => $products]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Iniziamo una transazione per garantire che tutto vada a buon fine
        $em->beginTransaction();

        try {
          // Salviamo l'ordine
          $em->persist($order);
          $em->flush();

          // Gestiamo i prodotti
          foreach ($order->getOrderProducts() as $orderProduct) {
            $product = $orderProduct->getProduct();
            $quantity = $orderProduct->getQuantity();

            if ($product->getStock() < $quantity) {
              throw new \Exception('Quantità maggiore dello stock disponibile per il prodotto ' . $product->getName());
            }

            // Creiamo la riga nella relazione OrderProduct
            $orderProduct->setOrder($order);
            $em->persist($orderProduct);

            // Aggiorniamo lo stock del prodotto
            $product->setStock($product->getStock() - $quantity);
            $em->persist($product);
          }

          // Commit della transazione
          $em->flush();
          $em->commit();

          // Redirect all'ordine creato o altra pagina
          return $this->redirectToRoute('order_success'); // Sostituisci con la tua route di successo
        } catch (\Exception $e) {
          // Se c'è un errore, annulliamo la transazione
          $em->rollback();

          // Mostriamo un messaggio di errore
          $this->addFlash('error', $e->getMessage());

          // Ritorniamo alla stessa pagina con il form
          return $this->redirectToRoute('order_create');
        }
    }

    return $this->render('order/create.html.twig', [
      'form' => $form->createView(),
      'products' => $products
    ]);
  }






  #[Route('/order/{id}/edit', name: 'order_edit')]
  public function edit(int $id, Request $request, EntityManagerInterface $em): Response
  {
    // Recupera il prodotto dal database
    $order = $em->getRepository(Order::class)->find($id);

    if (!$order) {
      throw $this->createNotFoundException('The order does not exist.');
    }

    // Usa il form per modificare il prodotto
    $form = $this->createForm(OrderType::class, $order);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em->flush();

      $this->addFlash('success', 'order updated successfully!');
      return $this->redirectToRoute('order_index');
    }

    return $this->render('order/edit.html.twig', [
      'form' => $form->createView(),
      'order' => $order,
    ]);
  }

  #[Route('/order/{id}', name: 'order_delete', methods: ['DELETE'])]
  public function delete(int $id, Request $request, EntityManagerInterface $em): RedirectResponse
  {
    // Recupera il prodotto dal database
    $order = $em->getRepository(Order::class)->find($id);

    if (!$order) {
      throw $this->createNotFoundException('The order does not exist.');
    }

    // Verifica che il token CSRF sia valido
    if ($this->isCsrfTokenValid('delete' . $order->getId(), $request->request->get('_token'))) {
      // Rimuovi il prodotto dal database
      $em->remove($order);
      $em->flush();

      // Flash message di successo
      $this->addFlash('success', 'order deleted successfully!');
    }

    // Redirect alla lista dei prodotti
    return $this->redirectToRoute('order_index');
  }

  #[Route('/order/{orderId}/add-product', name: 'order_add_product', methods: ['POST'])]
  public function addProductToOrder(int $orderId, Request $request): Response
  {
    $order = $this->getDoctrine()->getRepository(Order::class)->find($orderId);
    $productId = $request->request->get('productId');
    $quantity = $request->request->get('quantity');
    
    if (!$order) {
      throw $this->createNotFoundException('Order not found');
    }

    $product = $this->getDoctrine()->getRepository(Product::class)->find($productId);
    
    if (!$product) {
      throw $this->createNotFoundException('Product not found');
    }

    $orderProduct = new OrderProduct();
    $orderProduct->setOrder($order);
    $orderProduct->setProduct($product);
    $orderProduct->setQuantity($quantity);

    // Aggiungo il prodotto all'ordine
    $order->addOrderProduct($orderProduct);
    
    // Logica di stock
    if ($product->getStock() >= $quantity) {
      $product->setStock($product->getStock() - $quantity);  // Dedurre la quantità dal prodotto
    } else {
      $this->addFlash('error', 'Not enough stock available');
      return $this->redirectToRoute('order_view', ['orderId' => $orderId]);
    }

    // Salvo le modifiche
    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->persist($orderProduct);
    $entityManager->flush();

    return $this->redirectToRoute('order_view', ['orderId' => $orderId]);
  }
}
