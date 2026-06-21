<?php

/*
 * This file is part of the Personal Wallet project.
 */

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\Type\SetPasswordType;
use App\Form\Type\UserType;
use App\Service\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Administration of user accounts (data, roles, password).
 */
#[Route('/admin/user')]
#[IsGranted('ROLE_ADMIN')]
class UserController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param UserServiceInterface $userService User service
     * @param TranslatorInterface  $translator  Translator
     */
    public function __construct(private readonly UserServiceInterface $userService, private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Index.
     *
     * @param int $page Page
     *
     * @return Response Index
     */
    #[Route(name: 'admin_user_index', methods: ['GET'])]
    public function index(#[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->userService->getPaginatedList($page);

        return $this->render('admin/user/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Edit.
     *
     * @param Request $request Request
     * @param User    $user    User
     *
     * @return Response Edit
     */
    #[Route(
        '/{id}/edit',
        name: 'admin_user_edit',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['GET', 'PUT']
    )]
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(
            UserType::class,
            $user,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('admin_user_edit', ['id' => $user->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->save($user);
            $this->addFlash('success', $this->translator->trans('message.updated_successfully'));

            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render(
            'admin/user/edit.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }

    /**
     * Password.
     *
     * @param Request $request Request
     * @param User    $user    User
     *
     * @return Response Password
     */
    #[Route(
        '/{id}/password',
        name: 'admin_user_password',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['GET', 'PUT']
    )]
    public function password(Request $request, User $user): Response
    {
        $form = $this->createForm(
            SetPasswordType::class,
            null,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('admin_user_password', ['id' => $user->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('newPassword')->getData();
            $this->userService->changePassword($user, $plainPassword);
            $this->addFlash('success', $this->translator->trans('message.password_changed'));

            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render(
            'admin/user/password.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }
}
