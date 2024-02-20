<?php

namespace App\Form;

use App\Entity\EDT;
use App\Entity\Sejour;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use App\Entity\CollectionEDT;

class CollectionEDTType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
//        var_dump('COLLECTION !');
        $builder
//                ->add("test")
                ->add("collection", CollectionType::class, [
                    "entry_type" => EDTType::class,
                    'required'     => false,
                'label' => false,
//                    'mapped' => false,
            'prototype'    => true,
            'by_reference' => true,
            'attr'         => [
                'class' => 'actions-collection',
            ],
//                    "allow_add" => true,
//                    'allow_delete' => true,
//                    "prototype" => true,
//                    'attr'         => [
//                        'class' => 'collection',
//                    ],
//                    "empty_data" => function(FormInterface $form) {
//                        $edt = new EDT();
//                        $edt->setIdSejour($form->getParent()->getData());
//                        $edt->setDate(now());
//                        return $edt;
//                    }
                ]);
//        var_dump($builder);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CollectionEDT::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'CollectionEDTType';
    }
}
