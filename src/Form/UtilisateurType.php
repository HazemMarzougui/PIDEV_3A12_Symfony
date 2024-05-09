<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints\File as AssertFile; // Corrected namespace

class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', null, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Email']
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
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
            ->add('photo', FileType::class, [
                'required' => false,
                'mapped' => false, // Ensure 'mapped' is set to true if you're handling the file in the controller
                'label' => 'Photo', // Set a label for the field
                'constraints' => [
                    new AssertFile([ // Corrected constraint class name
                        'maxSize' => '5M', // Adjusted maximum file size to 5MB
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif'], // Allowed mime types for images
                        'mimeTypesMessage' => 'Veuillez télécharger une image au format JPG, PNG ou GIF', // Custom message for mime types validation
                    ]),
                ],
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
