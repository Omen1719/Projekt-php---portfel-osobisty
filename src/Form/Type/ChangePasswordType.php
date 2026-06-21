<?php

/*
 * This file is part of the Personal Wallet project.
 */

declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Form for changing the current user's password (verifies the current one).
 */
class ChangePasswordType extends AbstractType
{
    /**
     * Build form.
     *
     * @param FormBuilderInterface $builder Builder
     * @param array                $options Options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'currentPassword',
            PasswordType::class,
            [
                'label' => 'label.current_password',
                'mapped' => false,
                'constraints' => [new UserPassword()],
                'attr' => ['autocomplete' => 'current-password'],
            ]
        );

        $builder->add(
            'newPassword',
            RepeatedType::class,
            [
                'type' => PasswordType::class,
                'mapped' => false,
                'first_options' => [
                    'label' => 'label.new_password',
                    'attr' => ['autocomplete' => 'new-password'],
                ],
                'second_options' => [
                    'label' => 'label.repeat_password',
                    'attr' => ['autocomplete' => 'new-password'],
                ],
                'constraints' => [
                    new NotBlank(),
                    new Length(min: 6, max: 4096),
                ],
            ]
        );
    }

    /**
     * Configure options.
     *
     * @param OptionsResolver $resolver Resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => null]);
    }

    /**
     * Get block prefix.
     *
     * @return string Block prefix
     */
    public function getBlockPrefix(): string
    {
        return 'change_password';
    }
}
