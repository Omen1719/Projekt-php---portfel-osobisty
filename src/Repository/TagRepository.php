<?php

/*
 * This file is part of the Personal Wallet project.
 */

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Tag;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tag>
 */
class TagRepository extends ServiceEntityRepository
{
    public const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
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
        return $this->createQueryBuilder('tag')
            ->andWhere('tag.author = :author')
            ->setParameter('author', $author);
    }

    /**
     * Save.
     *
     * @param Tag $tag Tag
     */
    public function save(Tag $tag): void
    {
        $this->getEntityManager()->persist($tag);
        $this->getEntityManager()->flush();
    }

    /**
     * Delete.
     *
     * @param Tag $tag Tag
     */
    public function delete(Tag $tag): void
    {
        $this->getEntityManager()->remove($tag);
        $this->getEntityManager()->flush();
    }

    /**
     * Find one by title.
     *
     * @param string $title  Title
     * @param User   $author Author
     *
     * @return Tag|null One by title
     */
    public function findOneByTitle(string $title, User $author): ?Tag
    {
        return $this->findOneBy(['title' => $title, 'author' => $author]);
    }
}
