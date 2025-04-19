<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\UE;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('message')
            ->add('files', CollectionType::class, [
                'entry_type' => FileType::class,
                'allow_add' => true,
                'by_reference' => false, // so addFile() gets called
                'mapped' => false,       // because we will manually handle it
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
