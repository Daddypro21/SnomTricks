<?php

namespace App\Form;

use App\Entity\Imageupdate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class UpdateimageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('imageupdate', FileType::class, [
                'label' => 'image  (JPG,PNG FILE)',
                'mapped' => false,
                'required' => true,
                
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Imageupdate::class,
        ]);
    }
}
