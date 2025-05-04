<?php

namespace App\Form;

use App\Entity\UE;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType as SymfonyFileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UEType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', TextType::class, [
                'required' => true,
            ])
            ->add('title', TextType::class, [
                'required' => true,
            ])
            ->add('image_path', SymfonyFileType::class, [
                'label' => 'Upload Image',
                'mapped' => false, 
                'required' => false,
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UE::class,
        ]);
    }
}
