<?php

/*
 * This file is part of the Personal Wallet project.
 */

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

/**
 * Loads sample categories.
 */
class CategoryFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * Load data.
     */
    public function loadData(): void
    {
        if (!$this->manager instanceof ObjectManager || !$this->faker instanceof Generator) {
            return;
        }

        $this->createMany(20, 'category', function (int $i) {
            $category = new Category();
            $category->setTitle($this->faker->unique()->word());

            /** @var User $author */
            $author = $this->getRandomReference('user', User::class);
            $category->setAuthor($author);

            return $category;
        });
    }

    /**
     * Get dependencies.
     *
     * @return array Dependencies
     */
    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}
