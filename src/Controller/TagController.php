<?php

/*
 * This file is part of the Personal Wallet project.
 */

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Tag;
use App\Entity\User;
use App\Form\Type\TagType;
use App\Security\Voter\TagVoter;
use App\Service\TagServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Controller for managing the current user's tags.
 */
#[Route('/tag')]
class TagController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param TagServiceInterface $tagService Tag service
     * @param TranslatorInterface $translator Translator
     */
    public function __construct(private readonly TagServiceInterface $tagService, private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Index.
     *
     * @param int $page Page
     *
     * @return Response Index
     */
    #[Route(name: 'tag_index', methods: ['GET'])]
    public function index(#[MapQueryParameter] int $page = 1): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $pagination = $this->tagService->getPaginatedList($page, $user);

        return $this->render('tag/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Create.
     *
     * @param Request $request Request
     *
     * @return Response Create
     */
    #[Route('/create', name: 'tag_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $tag = new Tag();
        $form = $this->createForm(
            TagType::class,
            $tag,
            ['action' => $this->generateUrl('tag_create')]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();
            $tag->setAuthor($user);
            $this->tagService->save($tag);
            $this->addFlash('success', $this->translator->trans('message.created_successfully'));

            return $this->redirectToRoute('tag_index');
        }

        return $this->render('tag/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * View.
     *
     * @param Tag $tag Tag
     *
     * @return Response View
     */
    #[Route(
        '/{id}',
        name: 'tag_view',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['GET']
    )]
    public function view(Tag $tag): Response
    {
        $this->denyAccessUnlessGranted(TagVoter::VIEW, $tag);

        return $this->render('tag/view.html.twig', ['tag' => $tag]);
    }

    /**
     * Edit.
     *
     * @param Request $request Request
     * @param Tag     $tag     Tag
     *
     * @return Response Edit
     */
    #[Route(
        '/{id}/edit',
        name: 'tag_edit',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['GET', 'PUT']
    )]
    public function edit(Request $request, Tag $tag): Response
    {
        $this->denyAccessUnlessGranted(TagVoter::EDIT, $tag);

        $form = $this->createForm(
            TagType::class,
            $tag,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('tag_edit', ['id' => $tag->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->tagService->save($tag);
            $this->addFlash('success', $this->translator->trans('message.updated_successfully'));

            return $this->redirectToRoute('tag_index');
        }

        return $this->render(
            'tag/edit.html.twig',
            [
                'form' => $form->createView(),
                'tag' => $tag,
            ]
        );
    }

    /**
     * Delete.
     *
     * @param Request $request Request
     * @param Tag     $tag     Tag
     *
     * @return Response Delete
     */
    #[Route(
        '/{id}/delete',
        name: 'tag_delete',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['GET', 'DELETE']
    )]
    public function delete(Request $request, Tag $tag): Response
    {
        $this->denyAccessUnlessGranted(TagVoter::DELETE, $tag);

        $form = $this->createForm(
            FormType::class,
            $tag,
            [
                'method' => 'DELETE',
                'action' => $this->generateUrl('tag_delete', ['id' => $tag->getId()]),
                'validation_groups' => false,
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->tagService->delete($tag);
            $this->addFlash('success', $this->translator->trans('message.deleted_successfully'));

            return $this->redirectToRoute('tag_index');
        }

        return $this->render(
            'tag/delete.html.twig',
            [
                'form' => $form->createView(),
                'tag' => $tag,
            ]
        );
    }
}
