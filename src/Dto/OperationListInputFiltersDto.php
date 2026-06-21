<?php

/*
 * This file is part of the Personal Wallet project.
 */

declare(strict_types=1);

namespace App\Dto;

/**
 * Raw filter values coming from the query string.
 */
class OperationListInputFiltersDto
{
    /**
     * Constructor.
     *
     * @param int|null    $categoryId Category id
     * @param string|null $dateFrom   Date from
     * @param string|null $dateTo     Date to
     * @param int|null    $tagId      Tag id
     */
    public function __construct(public readonly ?int $categoryId = null, public readonly ?string $dateFrom = null, public readonly ?string $dateTo = null, public readonly ?int $tagId = null)
    {
    }
}
