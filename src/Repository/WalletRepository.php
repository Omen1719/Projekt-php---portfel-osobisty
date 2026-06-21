<?php

/*
 * This file is part of the Personal Wallet project.
 */

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use App\Entity\Wallet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Wallet>
 */
class WalletRepository extends ServiceEntityRepository
{
    public const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Wallet::class);
    }

    /**
     * Query all.
     *
     * @param User $author Author
     *
     * @return QueryBuilder Query all
     */
    public function queryAll(User $author): QueryBuilder
    {
        return $this->createQueryBuilder('wallet')
            ->andWhere('wallet.author = :author')
            ->setParameter('author', $author);
    }

    /**
     * Save.
     *
     * @param Wallet $wallet Wallet
     */
    public function save(Wallet $wallet): void
    {
        $this->getEntityManager()->persist($wallet);
        $this->getEntityManager()->flush();
    }

    /**
     * Delete.
     *
     * @param Wallet $wallet Wallet
     */
    public function delete(Wallet $wallet): void
    {
        $this->getEntityManager()->remove($wallet);
        $this->getEntityManager()->flush();
    }
}
