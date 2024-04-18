<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', null, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Email']
            ])
            ->add('password', PasswordType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Password']
            ])
            ->add('nom', null, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Nom']
            ])
            ->add('prenom', null, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Prenom']
            ])
            ->add('tel', null, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Telephone']
            ])
            ->add('adresse', null, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Adresse']
            ])
            ->add('dateNaiss', null, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('photo', null, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Photo']
            ])
            ->add('description', null, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Description']
            ])
            ->add('role', ChoiceType::class, [
                'choices' => [
                    'Admin' => 'Admin',
                    'Client' => 'Client',
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary mr-2']
            ]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
