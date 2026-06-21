<?php

/*
 * This file is part of the Personal Wallet project.
 */

declare(strict_types=1);

namespace App\Dto;

use App\Entity\Category;
use App\Entity\Tag;

/**
 * Resolved filters (entity objects, parsed dates) used to build the operations query.
 */
class OperationListFiltersDto
{
    /**
     * Constructor.
     *
     * @param Category|null           $category Category
     * @param \DateTimeImmutable|null $dateFrom Date from
     * @param \DateTimeImmutable|null $dateTo   Date to
     * @param Tag|null                $tag      Tag
     */
    public function __construct(public readonly ?Category $category = null, public readonly ?\DateTimeImmutable $dateFrom = null, public readonly ?\DateTimeImmutable $dateTo = null, public readonly ?Tag $tag = null)
    {
    }
}
