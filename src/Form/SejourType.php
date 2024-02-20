<?php

namespace App\Form;

use App\Entity\Sejour;
use App\Entity\Specialite;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Form\Extension\Core\Type\DateType;

class SejourType extends AbstractType
{
    
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        
        
        $builder
            ->add('date_debut', DateType::class, [
                'attr' => ['readonly' => true],
            ])
            ->add('date_fin', DateType::class, [
                'attr' => ['readonly' => true],
            ])
            ->add('motif')
            ->add(
                'id_specialite', EntityType::class,
                [
                    'class' => Specialite::class,
                    'choice_label' => 'label',
                    'label' => 'Spécialité Nécessaire',
                ]
            );
        
        $formModifier = function (FormInterface $form, Specialite $specialite): void {
            $utilisateur_repository = $this->entityManager->getRepository(Utilisateur::class);
            $utilsateurs = $utilisateur_repository->findBy(['id_specialite' => $specialite]);
            $form->add('id_medecin', EntityType::class, [
                'class' => Utilisateur::class,
                'required' => true,
                'choices' => $utilsateurs,
//                'choice_filter' => ChoiceList::filter(
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
                'choice_label' => function(Utilisateur $utilisateur) {
                    if($utilisateur->getIdType()->getLabel() == 'Médecin') {
                        return 'Dr. ' . $utilisateur->getPrenom() . ' ' . $utilisateur->getNom();
                    }
//                        return 'Dr. ' . $utilisateur.getPrenom() . ' ' . $utilisateur->getNom();
                },
                'label' => 'Médecin Souhaité',
            ]);
        };
        
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier): void {
                $data = $event->getData();
                if($data) {
                    $specialite_repository = $this->entityManager->getRepository(Specialite::class);
                    if(!$data->getIdSpecialite()) {
                        $specialite = $specialite_repository->findAll()[0];
                    } else {
                        $specialite = $data->getIdSpecialite();
                    }
                    
                    $formModifier($event->getForm(), $specialite);
                }
            }
        );
        
        $builder->get('id_specialite')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier): void {
                // It's important here to fetch $event->getForm()->getData(), as
                // $event->getData() will get you the client data (that is, the ID)
                $specialite = $event->getForm()->getData();
//var_dump($specialite);
                // since we've added the listener to the child, we'll have to pass on
                // the parent to the callback function!
                if($specialite) {
                    $formModifier($event->getForm()->getParent(), $specialite);
                }
            }
        );
//            ->add(
//                'id_medecin', EntityType::class,
//                [
//                    'class' => Utilisateur::class,
//                    
//                    
//                    'choice_filter' => ChoiceList::filter(
//                      $this,
//                      function($test) {
//                        if($test instanceof Utilisateur) {
////                            var_dump($this);
////                            var_dump($test);
////                            echo '_____________<br />';
//                            return $test->getIdType()->getLabel() == 'Médecin';
//                        }
//                        return false;
//                      },
//                      'test'
//                    ),
//                    'choice_label' => function(Utilisateur $utilisateur) {
//                        if($utilisateur->getIdType()->getLabel() == 'Médecin') {
//                            return 'Dr. ' . $utilisateur->getPrenom() . ' ' . $utilisateur->getNom();
//                        }
////                        return 'Dr. ' . $utilisateur.getPrenom() . ' ' . $utilisateur->getNom();
//                    },
//                ]
//            );
                    
                    
                    
//            $formModifier = function(FormInterface $form, Specialite $specialite): void {
//                
//                $form->get('id_medecin')->setData('test');
//            };
//            ->addEventSubscriber(FormEvents::POST_SET_DATA, function(FormEvent $event): void
//            {
//                $data = $event->getData();
//                    $form = $event->getForm();
//                    
//                    var_dump($data);
//            })
            
//            $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use($formModifier): void {
//                $data = $event->getData();
//                var_dump($data);
//                if($data) {
//                    $formModifier($event->getForm(), $data->getSpecialite());
//                }
//            });
//            $builder->get('id_specialite')->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event): void
//                {
//                    $data = $event->getData();
//                    $form = $event->getForm();
//                    
////                    var_dump($form->get('id_specialite'));
//                    
//                    $options = $form['id_specialite']->getData();
//                    
//                    
//                    
//                    var_dump($form->getParent());
//                    if($options)
//                    {
//                        var_dump($options->getIdMedecin());
//                    }
////                    var_dump($form['id_medecin']);
//                    
////                    var_dump($sejour);
////                    echo 'DATA : <br />';
////                    var_dump($data);
////                    echo '<br /><br />__________________________<br /><br />';
////                    echo 'FORM : <br />';
////                    var_dump($form);
////                    echo '<br /><br />__________________________<br /><br />';
////                    echo 'OPTIONS : <br />';
//                    var_dump($options);
////                    echo '<br /><br />__________________________<br /><br />';
//                    
//                }
//            );
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sejour::class,
        ]);
    }
    
    private function formMedecinModifier(Spcialite $specialite) {
        return array();
    }
}
