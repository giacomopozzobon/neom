<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\OrderProduct;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderProductType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    dump($options['products']);
    
    $builder
      ->add('product', ChoiceType::class, [ // Cambia SelectType con ChoiceType
        'choices' => $options['products'], // passiamo i prodotti disponibili come opzione
        'choice_label' => function (Product $product) {
          return $product->getName(); // mostriamo il nome del prodotto
        },
        'placeholder' => 'Scegli un prodotto',
      ])
      ->add('quantity', NumberType::class, [
        'attr' => ['min' => 1, 'max' => 100], // Impostiamo il range da 1 a 100
      ]);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
      'data_class' => OrderProduct::class,
      'products' => [], // aggiungiamo i prodotti come parametro
    ]);
  }
}
