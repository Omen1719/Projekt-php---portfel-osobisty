<?php

/*
 * This file is part of the Personal Wallet project.
 */

declare(strict_types=1);

namespace App\Controller;

use App\Dto\OperationListInputFiltersDto;
use App\Entity\Operation;
use App\Entity\User;
use App\Form\Type\OperationType;
use App\Security\Voter\OperationVoter;
use App\Service\CategoryServiceInterface;
use App\Service\OperationServiceInterface;
use App\Service\TagServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Controller for managing the current user's operations.
 */
#[Route('/operation')]
class OperationController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param OperationServiceInterface $operationService Operation service
     * @param CategoryServiceInterface  $categoryService  Category service
     * @param TagServiceInterface       $tagService       Tag service
     * @param TranslatorInterface       $translator       Translator
     */
    public function __construct(private readonly OperationServiceInterface $operationService, private readonly CategoryServiceInterface $categoryService, private readonly TagServiceInterface $tagService, private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Index.
     *
     * @param OperationListInputFiltersDto $filters Filters
     * @param int                          $page    Page
     *
     * @return Response Index
     */
    #[Route(name: 'operation_index', methods: ['GET'])]
    public function index(OperationListInputFiltersDto $filters, #[MapQueryParameter] int $page = 1): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $pagination = $this->operationService->getPaginatedList($page, $user, $filters);

        return $this->render(
            'operation/index.html.twig',
            [
                'pagination' => $pagination,
                'filters' => $filters,
                'balance' => $this->operationService->calculateBalance($user, $filters),
                'categories' => $this->categoryService->findAll($user),
                'tags' => $this->tagService->findAll($user),
            ]
        );
    }

    /**
     * Create.
     *
     * @param Request $request Request
     *
     * @return Response Create
     */
    #[Route('/create', name: 'operation_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $operation = new Operation();
        $form = $this->createForm(
            OperationType::class,
            $operation,
            [
                'action' => $this->generateUrl('operation_create'),
                'user' => $user,
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->operationService->wouldExceedFunds($operation)) {
                $this->addFlash('warning', $this->translator->trans('message.balance_below_zero'));

                return $this->render('operation/create.html.twig', ['form' => $form->createView()]);
            }

            $operation->setAuthor($user);
            $this->operationService->save($operation);
            $this->addFlash('success', $this->translator->trans('message.created_successfully'));

            return $this->redirectToRoute('operation_index');
        }

        return $this->render('operation/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * View.
     *
     * @param Operation $operation Operation
     *
     * @return Response View
     */
    #[Route(
        '/{id}',
        name: 'operation_view',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['GET']
    )]
    public function view(Operation $operation): Response
    {
        $this->denyAccessUnlessGranted(OperationVoter::VIEW, $operation);

        return $this->render('operation/view.html.twig', ['operation' => $operation]);
    }

    /**
     * Edit.
     *
     * @param Request   $request   Request
     * @param Operation $operation Operation
     *
     * @return Response Edit
     */
    #[Route(
        '/{id}/edit',
        name: 'operation_edit',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['GET', 'PUT']
    )]
    public function edit(Request $request, Operation $operation): Response
    {
        $this->denyAccessUnlessGranted(OperationVoter::EDIT, $operation);

        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(
            OperationType::class,
            $operation,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('operation_edit', ['id' => $operation->getId()]),
                'user' => $user,
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->operationService->wouldExceedFunds($operation)) {
                $this->addFlash('warning', $this->translator->trans('message.balance_below_zero'));

                return $this->render('operation/edit.html.twig', ['form' => $form->createView(), 'operation' => $operation]);
            }

            $this->operationService->save($operation);
            $this->addFlash('success', $this->translator->trans('message.updated_successfully'));

            return $this->redirectToRoute('operation_index');
        }

        return $this->render(
            'operation/edit.html.twig',
            [
                'form' => $form->createView(),
                'operation' => $operation,
            ]
        );
    }

    /**
     * Delete.
     *
     * @param Request   $request   Request
     * @param Operation $operation Operation
     *
     * @return Response Delete
     */
    #[Route(
        '/{id}/delete',
        name: 'operation_delete',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['GET', 'DELETE']
    )]
    public function delete(Request $request, Operation $operation): Response
    {
        $this->denyAccessUnlessGranted(OperationVoter::DELETE, $operation);

        $form = $this->createForm(
            FormType::class,
            $operation,
            [
                'method' => 'DELETE',
                'action' => $this->generateUrl('operation_delete', ['id' => $operation->getId()]),
                'validation_groups' => false,
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->operationService->delete($operation);
            $this->addFlash('success', $this->translator->trans('message.deleted_successfully'));

            return $this->redirectToRoute('operation_index');
        }

        return $this->render(
            'operation/delete.html.twig',
            [
                'form' => $form->createView(),
                'operation' => $operation,
            ]
        );
    }
}
