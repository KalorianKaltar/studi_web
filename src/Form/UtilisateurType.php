<?php

namespace App\Form;

use App\Entity\Specialite;
use App\Entity\TypeUtilisateur;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;


use Symfony\Component\Form\Extension\Core\Type\TextType;

class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
//            ->add('roles')
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => ['label' => 'Password', 'hash_property_path' => 'password'],
                'second_options' => ['label' => 'Repeat Password'],
                'mapped' => false,
            ])
            ->add('prenom')
            ->add('nom')
            ->add('adresse', TextType::class, [
                'required' => true,
            ])
            ->add('adresse_complement')
            ->add('code_postal', TextType::class, [
                'required' => true,
            ])
//            ->add('matricule')
//            ->add('id_type', EntityType::class, [
//                    'class' => TypeUtilisateur::class,
//                    'choice_label' => 'id',
//            ])
//            ->add('id_specialite', EntityType::class, [
//                    'class' => Specialite::class,
//                    'choice_label' => 'id',
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
