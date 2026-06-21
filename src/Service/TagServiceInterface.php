<?php

/*
 * This file is part of the Personal Wallet project.
 */

declare(strict_types=1);

namespace App\Service;

use App\Entity\Tag;
use App\Entity\User;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Contract for the tag service.
 */
interface TagServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int  $page   Page
     * @param User $author Author
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page, User $author): PaginationInterface;

    /**
     * Save.
     *
     * @param Tag $tag Tag
     */
    public function save(Tag $tag): void;

    /**
     * Delete.
     *
     * @param Tag $tag Tag
     */
    public function delete(Tag $tag): void;

    /**
     * Find one by title.
     *
     * @param string $title  Title
     * @param User   $author Author
     */
    public function findOneByTitle(string $title, User $author): ?Tag;

    /**
     * Find one by id.
     *
     * @param int $id Id
     */
    public function findOneById(int $id): ?Tag;

    /**
     * Find all.
     *
     * @param User $author Author
     *
     * @return array<int, Tag> List of tags
     */
    public function findAll(User $author): array;
}
