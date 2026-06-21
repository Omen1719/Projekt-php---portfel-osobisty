<?php

/*
 * This file is part of the Personal Wallet project.
 */

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Operation;
use App\Entity\Tag;
use App\Entity\User;
use App\Entity\Wallet;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

/**
 * Loads sample operations.
 */
class OperationFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * Load data.
     */
    public function loadData(): void
    {
        if (!$this->manager instanceof ObjectManager || !$this->faker instanceof Generator) {
            return;
        }

        $this->createMany(50, 'operation', function (int $i) {
            $operation = new Operation();
            $operation->setTitle($this->faker->sentence(3));
            $operation->setType((string) $this->faker->randomElement(Operation::TYPES));
            $operation->setAmount((string) $this->faker->randomFloat(2, 1, 2000));
            $operation->setDate(
                \DateTimeImmutable::createFromMutable(
                    $this->faker->dateTimeBetween('-100 days', 'now')
                )
            );

            /** @var Category $category */
            $category = $this->getRandomReference('category', Category::class);
            $operation->setCategory($category);

            /** @var Wallet $wallet */
            $wallet = $this->getRandomReference('wallet', Wallet::class);
            $operation->setWallet($wallet);

            /** @var User $author */
            $author = $this->getRandomReference('user', User::class);
            $operation->setAuthor($author);

            /** @var array<int, Tag> $tags */
            $tags = $this->getRandomReferenceList('tag', Tag::class, $this->faker->numberBetween(0, 4));
            foreach ($tags as $tag) {
                $operation->addTag($tag);
            }

            return $operation;
        });
    }

    /**
     * Get dependencies.
     *
     * @return array Dependencies
     */
    public function getDependencies(): array
    {
        return [CategoryFixtures::class, WalletFixtures::class, UserFixtures::class, TagFixtures::class];
    }
}
