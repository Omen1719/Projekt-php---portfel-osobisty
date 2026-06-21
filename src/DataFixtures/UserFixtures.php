<?php

/*
 * This file is part of the Personal Wallet project.
 */

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Enum\UserRole;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Loads the default administrator account.
 */
class UserFixtures extends AbstractBaseFixtures
{
    /**
     * Constructor.
     *
     * @param UserPasswordHasherInterface $passwordHasher Password hasher
     */
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    /**
     * Load data.
     */
    public function loadData(): void
    {
        if (!$this->manager instanceof ObjectManager || !$this->faker instanceof Generator) {
            return;
        }

        $this->createMany(1, 'user', function (int $i) {
            $user = new User();
            $user->setEmail('admin@example.com');
            $user->setRoles([UserRole::ROLE_ADMIN->value]);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'admin1234'));

            return $user;
        });
    }
}
