<?php

/*
 * This file is part of the Personal Wallet project.
 */

declare(strict_types=1);

namespace App\Service;

use App\Entity\Tag;
use App\Entity\User;
use App\Repository\TagRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Tag business logic: listing, persistence and lookup.
 */
class TagService implements TagServiceInterface
{
    private const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param TagRepository      $tagRepository Tag repository
     * @param PaginatorInterface $paginator     Knp paginator
     */
    public function __construct(private readonly TagRepository $tagRepository, private readonly PaginatorInterface $paginator)
    {
    }

    /**
     * Get a paginated list of the author's tags.
     *
     * @param int  $page   Page number
     * @param User $author Owner of the tags
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page, User $author): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->tagRepository->queryAll($author),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE,
            [
                'sortFieldAllowList' => ['tag.id', 'tag.createdAt', 'tag.updatedAt', 'tag.title'],
                'defaultSortFieldName' => 'tag.updatedAt',
                'defaultSortDirection' => 'desc',
            ]
        );
    }

    /**
     * Save a tag.
     *
     * @param Tag $tag Tag entity
     */
    public function save(Tag $tag): void
    {
        $this->tagRepository->save($tag);
    }

    /**
     * Delete a tag.
     *
     * @param Tag $tag Tag entity
     */
    public function delete(Tag $tag): void
    {
        $this->tagRepository->delete($tag);
    }

    /**
     * Find one tag by title for the given author.
     *
     * @param string $title  Tag title
     * @param User   $author Owner of the tag
     *
     * @return Tag|null Tag entity or null
     */
    public function findOneByTitle(string $title, User $author): ?Tag
    {
        return $this->tagRepository->findOneByTitle($title, $author);
    }

    /**
     * Find one tag by its identifier.
     *
     * @param int $id Tag identifier
     *
     * @return Tag|null Tag entity or null
     */
    public function findOneById(int $id): ?Tag
    {
        return $this->tagRepository->find($id);
    }

    /**
     * Find all tags of the given author.
     *
     * @param User $author Owner of the tags
     *
     * @return array<int, Tag> List of tags
     */
    public function findAll(User $author): array
    {
        return $this->tagRepository->findBy(['author' => $author], ['title' => 'ASC']);
    }
}
