<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType as SymfonyFileType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Message' => 'message',
                    'Fichier' => 'fichier',
                ],
                'placeholder' => 'Choisissez un type',
                'required' => true,
                'attr' => [
                    'data-action' => 'post#changerType' // important pour Stimulus
                ],
            ])
            ->add('message')
            ->add('file_path', SymfonyFileType::class, [
                'label' => 'Upload File',
                'mapped' => false, // important: on gère l'upload manuellement
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
