<?php

/*
 * This file is part of the Personal Wallet project.
 */

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Enum\UserRole;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * User form used by the administrator (e-mail and roles/permissions).
 */
class UserType extends AbstractType
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
            'email',
            EmailType::class,
            [
                'label' => 'label.email',
                'required' => true,
            ]
        );

        $builder->add(
            'roles',
            ChoiceType::class,
            [
                'label' => 'label.roles',
                'choices' => [
                    UserRole::ROLE_ADMIN->label() => UserRole::ROLE_ADMIN->value,
                    UserRole::ROLE_USER->label() => UserRole::ROLE_USER->value,
                ],
                'multiple' => true,
                'expanded' => true,
                'required' => false,
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
        $resolver->setDefaults(['data_class' => User::class]);
    }

    /**
     * Get block prefix.
     *
     * @return string Block prefix
     */
    public function getBlockPrefix(): string
    {
        return 'user';
    }
}
