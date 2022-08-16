<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            
            ->add('firstName',TextType::class,['attr' => ['class' => 'bg-light form-text']])
            ->add('lastName',TextType::class,['attr' => ['class' => 'bg-light form-text']])
            ->add('email',TextType::class,['attr' => ['class' => 'bg-light form-text']])
            // ->add('roles')
            // ->add('password')
            // ->add('createdAt')
            // ->add('updatedAt')
            // ->add('isVerified')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
