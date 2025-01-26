<?php

namespace App\Form;

use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Form\OrderProductType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Order Name'
                ]
            ])
            ->add('description', TextType::class, [
                'required' => false, 
                'attr' => [
                    'class' => 'form-control', 
                    'placeholder' => 'Description'
                ]
            ])
            ->add('date', DateType::class, [
                'attr' => ['class' => 'form-control'],
                'widget' => 'single_text', 
                'format' => 'yyyy-MM-dd'
            ])
            ->add('orderProducts', CollectionType::class, [
                'entry_type' => OrderProductType::class,
                'entry_options' => ['products' => $options['products']], // passiamo i prodotti disponibili
                'allow_add' => true, // Permettiamo di aggiungere più prodotti
                'by_reference' => false,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save Order', // Cambiamo il testo del bottone per riflettere l'azione
                'attr' => ['class' => 'btn btn-primary']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
            'products' => [], // passiamo i prodotti come parametro
        ]);
    }
}
