<?php

/*
 * This file is part of the Personal Wallet project.
 */

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Category;
use App\Entity\Operation;
use App\Entity\User;
use App\Entity\Wallet;
use App\Form\DataTransformer\TagsDataTransformer;
use App\Repository\CategoryRepository;
use App\Repository\WalletRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form for creating and editing an operation.
 */
class OperationType extends AbstractType
{
    /**
     * Constructor.
     *
     * @param TagsDataTransformer $tagsDataTransformer Tags data transformer
     */
    public function __construct(private readonly TagsDataTransformer $tagsDataTransformer)
    {
    }

    /**
     * Build form.
     *
     * @param FormBuilderInterface $builder Builder
     * @param array                $options Options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User $user */
        $user = $options['user'];

        $builder->add(
            'title',
            TextType::class,
            [
                'label' => 'label.title',
                'required' => true,
                'attr' => ['max_length' => 255],
            ]
        );

        $builder->add(
            'amount',
            NumberType::class,
            [
                'label' => 'label.amount',
                'required' => true,
                'scale' => 2,
                'html5' => true,
                'attr' => ['step' => '0.01', 'min' => '0'],
            ]
        );

        $builder->add(
            'type',
            ChoiceType::class,
            [
                'label' => 'label.operation_type',
                'choices' => [
                    'label.operation_income' => Operation::TYPE_INCOME,
                    'label.operation_expense' => Operation::TYPE_EXPENSE,
                ],
                'required' => true,
            ]
        );

        $builder->add(
            'note',
            TextareaType::class,
            [
                'label' => 'label.note',
                'required' => false,
                'attr' => ['rows' => 4],
                'help' => 'message.markdown_supported',
            ]
        );

        $builder->add(
            'date',
            DateType::class,
            [
                'label' => 'label.date',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'required' => true,
            ]
        );

        $builder->add(
            'wallet',
            EntityType::class,
            [
                'class' => Wallet::class,
                'choice_label' => fn (Wallet $wallet): string => (string) $wallet->getTitle(),
                'query_builder' => fn (WalletRepository $repository): QueryBuilder => $repository->createQueryBuilder('wallet')
                    ->where('wallet.author = :author')
                    ->setParameter('author', $user)
                    ->orderBy('wallet.title', 'ASC'),
                'label' => 'label.wallet',
                'placeholder' => 'label.none',
                'required' => true,
            ]
        );

        $builder->add(
            'category',
            EntityType::class,
            [
                'class' => Category::class,
                'choice_label' => fn (Category $category): string => (string) $category->getTitle(),
                'query_builder' => fn (CategoryRepository $repository): QueryBuilder => $repository->createQueryBuilder('category')
                    ->where('category.author = :author')
                    ->setParameter('author', $user)
                    ->orderBy('category.title', 'ASC'),
                'label' => 'label.category',
                'placeholder' => 'label.none',
                'required' => true,
            ]
        );

        $builder->add(
            'tags',
            TextType::class,
            [
                'label' => 'label.tags',
                'required' => false,
                'attr' => ['max_length' => 255],
                'help' => 'message.tags_hint',
            ]
        );
        $builder->get('tags')->addModelTransformer($this->tagsDataTransformer);
    }

    /**
     * Configure options.
     *
     * @param OptionsResolver $resolver Resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Operation::class]);
        $resolver->setRequired('user');
        $resolver->setAllowedTypes('user', User::class);
    }

    /**
     * Get block prefix.
     *
     * @return string Block prefix
     */
    public function getBlockPrefix(): string
    {
        return 'operation';
    }
}
