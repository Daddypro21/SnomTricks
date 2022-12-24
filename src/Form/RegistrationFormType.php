<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName',TextType::class,['label'=>'Prenom','attr' => ['class' => 'bg-light form-text']])
            ->add('lastName',TextType::class,['label'=>'Nom','attr' => ['class' => 'bg-light form-text']])
            ->add('email',EmailType::class,['label'=>'Email','attr' => ['class' => 'bg-light form-text']])
            ->add('agreeTerms', CheckboxType::class, [
                'label'=> 'j\'accepte les conditions d\'utilisation',
                'mapped' => false,
                'attr' => ['class' => 'check-b'],
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devrez accepter nos conditions d\'utilisation.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'label'=>'Mot de passe',
                'attr' => ['autocomplete' => 'new-password'],
                'attr' => ['class' => 'form-text bg-light'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'S\'il vous plait entrez un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit avoir aumoins {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
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
