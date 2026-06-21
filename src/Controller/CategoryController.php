<?php

/*
 * This file is part of the Personal Wallet project.
 */

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Category;
use App\Entity\User;
use App\Form\Type\CategoryType;
use App\Security\Voter\CategoryVoter;
use App\Service\CategoryServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Controller for managing the current user's categories.
 */
#[Route('/category')]
class CategoryController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param CategoryServiceInterface $categoryService Category service
     * @param TranslatorInterface      $translator      Translator
     */
    public function __construct(private readonly CategoryServiceInterface $categoryService, private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Index.
     *
     * @param int $page Page
     *
     * @return Response Index
     */
    #[Route(name: 'category_index', methods: ['GET'])]
    public function index(#[MapQueryParameter] int $page = 1): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $pagination = $this->categoryService->getPaginatedList($page, $user);

        return $this->render('category/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Create.
     *
     * @param Request $request Request
     *
     * @return Response Create
     */
    #[Route('/create', name: 'category_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(
            CategoryType::class,
            $category,
            ['action' => $this->generateUrl('category_create')]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();
            $category->setAuthor($user);
            $this->categoryService->save($category);
            $this->addFlash('success', $this->translator->trans('message.created_successfully'));

            return $this->redirectToRoute('category_index');
        }

        return $this->render('category/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * View.
     *
     * @param Category $category Category
     *
     * @return Response View
     */
    #[Route(
        '/{id}',
        name: 'category_view',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['GET']
    )]
    public function view(Category $category): Response
    {
        $this->denyAccessUnlessGranted(CategoryVoter::VIEW, $category);

        return $this->render(
            'category/view.html.twig',
            [
                'category' => $category,
                'can_be_deleted' => $this->categoryService->canBeDeleted($category),
            ]
        );
    }

    /**
     * Edit.
     *
     * @param Request  $request  Request
     * @param Category $category Category
     *
     * @return Response Edit
     */
    #[Route(
        '/{id}/edit',
        name: 'category_edit',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['GET', 'PUT']
    )]
    public function edit(Request $request, Category $category): Response
    {
        $this->denyAccessUnlessGranted(CategoryVoter::EDIT, $category);

        $form = $this->createForm(
            CategoryType::class,
            $category,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('category_edit', ['id' => $category->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryService->save($category);
            $this->addFlash('success', $this->translator->trans('message.updated_successfully'));

            return $this->redirectToRoute('category_index');
        }

        return $this->render(
            'category/edit.html.twig',
            [
                'form' => $form->createView(),
                'category' => $category,
            ]
        );
    }

    /**
     * Delete.
     *
     * @param Request  $request  Request
     * @param Category $category Category
     *
     * @return Response Delete
     */
    #[Route(
        '/{id}/delete',
        name: 'category_delete',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['GET', 'DELETE']
    )]
    public function delete(Request $request, Category $category): Response
    {
        $this->denyAccessUnlessGranted(CategoryVoter::DELETE, $category);

        if (!$this->categoryService->canBeDeleted($category)) {
            $this->addFlash('warning', $this->translator->trans('message.category_contains_tasks'));

            return $this->redirectToRoute('category_index');
        }

        $form = $this->createForm(
            FormType::class,
            $category,
            [
                'method' => 'DELETE',
                'action' => $this->generateUrl('category_delete', ['id' => $category->getId()]),
                'validation_groups' => false,
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryService->delete($category);
            $this->addFlash('success', $this->translator->trans('message.deleted_successfully'));

            return $this->redirectToRoute('category_index');
        }

        return $this->render(
            'category/delete.html.twig',
            [
                'form' => $form->createView(),
                'category' => $category,
            ]
        );
    }
}
