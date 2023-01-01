<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('new_password', RepeatedType::class, [ // RepeatedType permet de répéter 2 fois un input
                'type' => PasswordType::class, // Ici on précise que les 2 inputs seront de type password
                'invalid_message' => 'Le mot de passe et le confirmation doivent être identique', // Le message sera sur le 1er input
                'required' => true,
                'first_options' => [ // Ici on configure le 1er input
                    'label' => 'Nouveau mot de passe',
                    'attr' => [
                        'placeholder' => '************'
                    ]
                ],
                'second_options' => [ // Ici on configure le 2èmes input
                    'label' => 'Confirmez votre nouveau mot de passe',
                    'attr' => [
                        'placeholder' => '************'
                    ]
                ],
                
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Mettre à jour",
                'attr' => [
                    'class' => 'btn-block btn-info'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
