<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

final class ProductController extends AbstractController
{


  #[Route('/product', name: 'product_index')]
  public function index(EntityManagerInterface $em): Response
  {
    $products = $em->getRepository(Product::class)->findAll();

    return $this->render('product/index.html.twig', [
      'products' => $products,
    ]);
  }




  #[Route('/product/create', name: 'product_create')]
  public function create(Request $request, EntityManagerInterface $em): Response
  {
    $product = new Product();
    $form = $this->createForm(ProductType::class, $product);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em->persist($product);
      $em->flush();

      $this->addFlash('success', 'Product created successfully!');
      return $this->redirectToRoute('product_index');
    }

    return $this->render('product/create.html.twig', [
      'form' => $form->createView(),
    ]);
  }




  #[Route('/product/{id}/edit', name: 'product_edit')]
  public function edit(int $id, Request $request, EntityManagerInterface $em): Response
  {
    // Recupera il prodotto dal database
    $product = $em->getRepository(Product::class)->find($id);

    if (!$product) {
      throw $this->createNotFoundException('The product does not exist.');
    }

    // Usa il form per modificare il prodotto
    $form = $this->createForm(ProductType::class, $product);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em->flush();

      $this->addFlash('success', 'Product updated successfully!');
      return $this->redirectToRoute('product_index');
    }

    return $this->render('product/edit.html.twig', [
      'form' => $form->createView(),
      'product' => $product,
    ]);
  }

  #[Route('/product/{id}', name: 'product_delete', methods: ['DELETE'])]
  public function delete(int $id, Request $request, EntityManagerInterface $em): RedirectResponse
  {
    // Recupera il prodotto dal database
    $product = $em->getRepository(Product::class)->find($id);

    if (!$product) {
      throw $this->createNotFoundException('The product does not exist.');
    }

    // Verifica che il token CSRF sia valido
    if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
      // Rimuovi il prodotto dal database
      $em->remove($product);
      $em->flush();

      // Flash message di successo
      $this->addFlash('success', 'Product deleted successfully!');
    }

    // Redirect alla lista dei prodotti
    return $this->redirectToRoute('product_index');
  }
}
