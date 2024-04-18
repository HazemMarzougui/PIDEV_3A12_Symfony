<?php

namespace App\Form;

use App\Entity\Offre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class OffreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('montantRemise', null, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le montant de la remise est obligatoire.']),
                    new Assert\Type(['type' => 'numeric', 'message' => 'Le montant de la remise doit être numérique.']),
                ],
            ])
            ->add('dateDebut', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('dateFin', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
                'constraints' => [
                    new Assert\Callback([
                        'callback' => function ($dateFin, $context) {
                            $dateDebut = $context->getRoot()->getData()->getDateDebut();
                            if ($dateFin !== null && $dateDebut !== null && $dateFin < $dateDebut) {
                                $context->buildViolation('La date de fin doit être supérieure ou égale à la date de début.')
                                    ->atPath('dateFin')
                                    ->addViolation();
                            }
                        }
                    ])
                ]
            ])
            ->add('description', null, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La description est obligatoire.']),
                ],
            ])
            ->add('evenement')
            ->add('idProduitOffre')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Offre::class, 
        ]);
    }
}
