<?php

/*
 * This file is part of the Personal Wallet project.
 */

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * User business logic: pagination, persistence and password hashing.
 */
class UserService implements UserServiceInterface
{
    private const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param UserRepository              $userRepository User repository
     * @param UserPasswordHasherInterface $passwordHasher Password hasher
     * @param PaginatorInterface          $paginator      Paginator
     */
    public function __construct(private readonly UserRepository $userRepository, private readonly UserPasswordHasherInterface $passwordHasher, private readonly PaginatorInterface $paginator)
    {
    }

    /**
     * Get paginated list.
     *
     * @param int $page Page
     *
     * @return PaginationInterface Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->userRepository->queryAll(),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE,
            [
                'sortFieldAllowList' => ['user.id', 'user.email'],
                'defaultSortFieldName' => 'user.id',
                'defaultSortDirection' => 'asc',
            ]
        );
    }

    /**
     * Save.
     *
     * @param User $user User
     */
    public function save(User $user): void
    {
        $this->userRepository->save($user);
    }

    /**
     * Change password.
     *
     * @param User   $user          User
     * @param string $plainPassword Plain password
     */
    public function changePassword(User $user, string $plainPassword): void
    {
        $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));
        $this->userRepository->save($user);
    }
}
