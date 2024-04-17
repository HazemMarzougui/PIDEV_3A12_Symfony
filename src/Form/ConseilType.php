<?php

namespace App\Form;

use App\Entity\Conseil;
use App\Entity\Typeconseil;
use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\MimeType;

class ConseilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('nomConseil', null, [
            'label' => 'Nom Conseil',
            'attr' => ['class' => 'form-control', 'placeholder' => 'Entrer nom conseil'],
        ])
        ->add('video', FileType::class, [
            'required' => false,
            'mapped' => false, // Ensure 'mapped' is set to true
            'label' => 'Video', // Set a label for the field
            'constraints' => [
                new File([
                    'maxSize' => '5000000k',
                    'mimeTypes' => ['video/mp4'],
                    'mimeTypesMessage' => 'Please upload a valid MP4 video file',
                ]),
            ],
            'attr' => [
                'class' => 'form-control', // Use 'form-control-file' class for file input type
            ],
        ])
        
        ->add('description', null, [
            'label' => 'Description',
            'attr' => ['class' => 'form-control', 'placeholder' => 'Entrer description'],
        ])
        ->add('idTypec', EntityType::class, [
            'label' => 'Selectionner Categorie',
            'class' => Typeconseil::class,
            'choice_label' => 'nomtypec',
            'attr' => ['class' => 'form-control', 'style' => 'width: 450px;'],
        ])
        ->add('idProduit', EntityType::class, [
            'label' => 'Selectionner Produit',
            'class' => Produit::class,
            'choice_label' => 'nomProduit',
            'attr' => ['class' => 'form-control', 'style' => 'width: 450px;'],
        ])
        ->add('Enregistrer', SubmitType::class, [
            'label' => '<i class="mdi mdi-library-plus btn-icon-prepend"></i> Enregistrer', // Include icon in button label
            'label_html' => true, // Allow HTML in button label
            'attr' => [
                'class' => 'btn btn-success btn-icon-text', // Apply classes
                // Add other attributes as needed
                'style' => 'position: absolute; bottom: -47px; right: 95px;', // Positioning
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Conseil::class,
        ]);
    }
}
