<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class ProductType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('name', TextType::class, [
        'constraints' => [
          new NotBlank(),
          new Type(['type' => 'string']),
        ],
        'attr' => ['class' => 'form-control', 'placeholder' => 'Enter product stock'],
      ])
      ->add('price', NumberType::class, [
        'constraints' => [
          new NotBlank(),
          new Type(['type' => 'float']),
        ],
        'attr' => ['class' => 'form-control', 'placeholder' => 'Enter product stock'],
      ])
      ->add('stock', IntegerType::class, [
        'constraints' => [
            new NotBlank(),
            new Type(['type' => 'integer']),
        ],
        'attr' => ['class' => 'form-control', 'placeholder' => 'Enter product stock'],
      ])
      // Aggiungiamo il pulsante di submit nel form
      ->add('save', SubmitType::class, [
        'label' => 'Save Product',
        'attr' => ['class' => 'btn btn-primary'],
      ]);
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => Product::class,
    ]);
  }
}
