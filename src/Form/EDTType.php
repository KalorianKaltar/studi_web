<?php

namespace App\Form;

use App\Entity\EDT;
use App\Entity\Sejour;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


use Symfony\Component\Form\Extension\Core\Type\DateType;

use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

use App\Entity\Specialite;


class EDTType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
//        var_dump('EDT !');
        $builder
            ->add('date', DateType::class, [
                'attr' => ['readonly' => true],
            ])
            ->add(
                'id_sejour', EntityType::class, [
                    'class' => Sejour::class,
                    'choice_label' => 'id',
                    'attr' => ['class' => 'hidden-field', 'readonly' => true],
                    'label' => false,
                ]
            )
            ->add(
                'id_medecin', EntityType::class, [
                    'class' => Utilisateur::class,
                    'choice_label' => function(Utilisateur $utilisateur) {
                        if($utilisateur->getIdType()->getLabel() == 'Médecin') {
                            return 'Dr. ' . $utilisateur->getPrenom() . ' ' . $utilisateur->getNom();
                        }    
                    },
//                    'choice_filter' => ChoiceList::filter(
//                      $this,
//                      function($test) use ($specialite) {
//                            
//                        if($test instanceof Utilisateur) {
//                            return $test->getIdType()->getLabel() == 'Médecin' && $test->getIdSpecialite() == $specialite;
//                        }
//                        return false;
//                      },
//                      ['medecin']
//                    ),
            ])
        ;
                    
                    
        $formModifier = function (FormInterface $form, Specialite $specialite): void {
            $form->add('id_medecin', EntityType::class, [
                'class' => Utilisateur::class,
//                'placeholder' => '',
                'required' => true,
                'choice_filter' => ChoiceList::filter(
                      $this,
                      function($test) use ($specialite) {
                            
                        if($test instanceof Utilisateur) {
                            return $test->getIdType()->getLabel() == 'Médecin' && $test->getIdSpecialite() == $specialite;
                        }
                        return false;
                      },
                      ['medecin']
                    ),
                    'choice_label' => function(Utilisateur $utilisateur) {
                        if($utilisateur->getIdType()->getLabel() == 'Médecin') {
                            return 'Dr. ' . $utilisateur->getPrenom() . ' ' . $utilisateur->getNom();
                        }
//                        return 'Dr. ' . $utilisateur.getPrenom() . ' ' . $utilisateur->getNom();
                    },
            ]);
        };
        
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier): void {
                $data = $event->getData();
                if($data) {
                    if(!$data->getIdSejour()->getIdSpecialite()) {
                        $specialite_repository = $this->entityManager->getRepository(Specialite::class);
                        $specialite = $specialite_repository->findAll()[0];
                    } else {
                        $specialite = $data->getIdSejour()->getIdSpecialite();
                    }
                    
                    $formModifier($event->getForm(), $specialite);
                }
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EDT::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'EDTType';
    }
}
