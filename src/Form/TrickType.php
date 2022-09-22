<?php

namespace App\Form;

use App\Entity\Trick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title',TextType::class,['label'=>'Titre','attr' => ['class' => 'form-text']])
            ->add('description',TextareaType::class,['label'=>'Description','attr' => ['class' => 'commentform']])
            ->add('cover', FileType::class, [
                'label' => 'image de couverture (JPG,PNG FILE)',
                'mapped' => false,
                'required' => true,
                // 'constraints' => [
                //     new File([
                //         'maxSize' => '1024k',
                //         'mimeTypes' => [
                //             'application/jpg',
                //             'application/png',
                //         ],
                //         'mimeTypesMessage' => 'Please upload a valid image',
                //     ])
                // ]
                ])
            ->add('images', FileType::class, [
                'label' => 'image (JPG,PNG FILE)',
                'mapped' => false,
                'required' => true,
                // 'constraints' => [
                //     new File([
                //         'maxSize' => '1024k',
                //         'mimeTypes' => [
                //             'application/jpg',
                //             'application/png',
                //         ],
                //         'mimeTypesMessage' => 'Please upload a valid image',
                //     ])
                // ]
                ])

                ->add('video', TextType::class, [
                    'label' => 'Url (youtube video)',
                    'mapped' => false,
                    'required' => true,
                ])
            //->add('createdAt')
            //->add('UpdateAt')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
