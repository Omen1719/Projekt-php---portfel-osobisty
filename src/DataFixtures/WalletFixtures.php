<?php

/*
 * This file is part of the Personal Wallet project.
 */

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Wallet;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

/**
 * Loads sample wallets.
 */
class WalletFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * Load data.
     */
    public function loadData(): void
    {
        if (!$this->manager instanceof ObjectManager || !$this->faker instanceof Generator) {
            return;
        }

        $this->createMany(5, 'wallet', function (int $i) {
            $wallet = new Wallet();
            $wallet->setTitle(ucfirst((string) $this->faker->unique()->word()));
            $wallet->setType((string) $this->faker->randomElement(Wallet::TYPES));

            /** @var User $author */
            $author = $this->getRandomReference('user', User::class);
            $wallet->setAuthor($author);

            return $wallet;
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
