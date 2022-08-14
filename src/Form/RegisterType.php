<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Votre prénom',
                'attr' => [
                    'placeholder' => 'Jhon'
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Votre nom',
                'attr' => [
                    'placeholder' => 'Doe'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Votre email',
                'attr' => [
                    'placeholder' => 'jDoe@gmail.com'
                ]
            ])
            ->add('password', RepeatedType::class, [ // RepeatedType permet de répéter 2 fois un input
                'type' => PasswordType::class, // Ici on précise que les 2 inputs seront de type password
                'invalid_message' => 'Le mot de passe et le confirmation doivent être identique', // Le message sera sur le 1er input
                'required' => true,
                'first_options' => [ // Ici on configure le 1er input
                    'label' => 'Mot de passe',
                    'attr' => [
                        'placeholder' => '************'
                    ]
                ],
                'second_options' => [ // Ici on configure le 2èmes input
                    'label' => 'Confirmez votre mot de passe',
                    'attr' => [
                        'placeholder' => '************'
                    ]
                ],
                
            ])
            ->add('submit', SubmitType::class, [
                'label' => "S'inscrire"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
