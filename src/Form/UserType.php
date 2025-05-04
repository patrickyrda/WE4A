<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Student' => 'ROLE_STUDENT',
                    'Teacher' => 'ROLE_TEACHER',
                    'Admin' => 'ROLE_ADMIN',
                ],
                'expanded' => true, // checkboxes (set false if you want a dropdown)
                'multiple' => true, // allows selecting more than one role
                'label' => 'Roles',
            ])
            ->add('plainPassword', PasswordType::class, [
            'mapped' => false,
            'required' => false,
            'attr' => [
                'autocomplete' => 'new-password',
                'placeholder' => 'password120',
            ],
            'constraints' => [
                new Length([
                    'min' => 6,
                    'minMessage' => 'Your password should be at least {{ limit }} characters',
                    'max' => 4096,
                ]),
            ],
            ])
            ->add('name')
            ->add('surname')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
