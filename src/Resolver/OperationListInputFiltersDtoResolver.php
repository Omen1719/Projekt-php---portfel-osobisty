<?php

/*
 * This file is part of the Personal Wallet project.
 */

declare(strict_types=1);

namespace App\Resolver;

use App\Dto\OperationListInputFiltersDto;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

/**
 * Builds an OperationListInputFiltersDto from the request query string.
 */
class OperationListInputFiltersDtoResolver implements ValueResolverInterface
{
    /**
     * Resolve.
     *
     * @param Request          $request  Request
     * @param ArgumentMetadata $argument Argument
     *
     * @return iterable<OperationListInputFiltersDto>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $argumentType = $argument->getType();
        if (!$argumentType || !is_a($argumentType, OperationListInputFiltersDto::class, true)) {
            return [];
        }

        $categoryId = $request->query->get('categoryId');
        $dateFrom = $request->query->get('dateFrom');
        $dateTo = $request->query->get('dateTo');
        $tagId = $request->query->get('tagId');

        return [new OperationListInputFiltersDto(
            null !== $categoryId && '' !== $categoryId ? (int) $categoryId : null,
            $dateFrom ?: null,
            $dateTo ?: null,
            null !== $tagId && '' !== $tagId ? (int) $tagId : null,
        ), ];
    }
}
