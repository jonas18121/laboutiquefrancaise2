<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Quel nom souhaitez-vous donner à votre adresse ?',
                'attr' => [
                    'placerholder' => 'Nommez votre adresse'
                ]
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Votre prénom',
                'attr' => [
                    'placerholder' => 'Entrez votre prénom'
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Votre nom',
                'attr' => [
                    'placerholder' => 'Entrez votre prénom'
                ]
            ])
            ->add('company', TextType::class, [
                'label' => 'Votre société (facultatif)',
                'attr' => [
                    'placerholder' => 'entrez le nom de votre société'
                ]
            ])
            ->add('address', TextType::class, [
                'label' => 'Votre adresse',
                'attr' => [
                    'placerholder' => 'Entrez votre adresse'
                ]
            ])
            ->add('postal', TextType::class, [
                'label' => 'Votre  code postal',
                'attr' => [
                    'placerholder' => 'Votre  code postal'
                ]
            ])
            ->add('city', TextType::class, [
                'label' => 'Votre  ville',
                'attr' => [
                    'placerholder' => 'Votre ville'
                ]
            ])
            ->add('country', CountryType::class, [
                'label' => 'Pays',
                'attr' => [
                    'placerholder' => 'Votre Pays',
                    'class' => "form-control"
                ]
            ])
            ->add('phone', TelType::class, [
                'label' => 'Votre téléphone',
                'attr' => [
                    'placerholder' => 'Votre téléphone'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider',
                'attr' => [
                    'class' => 'btn btn-block btn-info'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
