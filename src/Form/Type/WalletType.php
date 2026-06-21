<?php

/*
 * This file is part of the Personal Wallet project.
 */

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Wallet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form for creating and editing a wallet.
 */
class WalletType extends AbstractType
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
            'title',
            TextType::class,
            [
                'label' => 'label.title',
                'required' => true,
                'attr' => ['max_length' => 64],
            ]
        );

        $builder->add(
            'type',
            ChoiceType::class,
            [
                'label' => 'label.wallet_type',
                'choices' => [
                    'label.wallet_card' => Wallet::TYPE_CARD,
                    'label.wallet_cash' => Wallet::TYPE_CASH,
                ],
                'required' => true,
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
        $resolver->setDefaults(['data_class' => Wallet::class]);
    }

    /**
     * Get block prefix.
     *
     * @return string Block prefix
     */
    public function getBlockPrefix(): string
    {
        return 'wallet';
    }
}
