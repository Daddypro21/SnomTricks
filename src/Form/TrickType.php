<?php

namespace App\Form;

use App\Entity\Trick;
use App\Form\VideoType;
use App\Form\ImageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

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
                
                ])
            ->add('images', CollectionType::class, [
                'entry_type' => ImageType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'attr' => [
                    'class' => 'collection'
                ],
                'by_reference' => false
            ])
            ->add('videos', CollectionType::class, [
                'entry_type' => VideoType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'attr' => [
                    'class' => 'collection'
                ],
                'by_reference' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }

    

    

}
