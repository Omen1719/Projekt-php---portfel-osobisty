<?php

/*
 * This file is part of the Personal Wallet project.
 */

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\ChangePasswordType;
use App\Form\Type\ProfileType;
use App\Service\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Current user's profile controller (account data and password).
 */
#[Route('/profile')]
class ProfileController extends AbstractController
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
     * @return Response Index
     */
    #[Route(name: 'profile_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('profile/index.html.twig', ['user' => $this->getUser()]);
    }

    /**
     * Edit.
     *
     * @param Request $request Request
     *
     * @return Response Edit
     */
    #[Route('/edit', name: 'profile_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(
            ProfileType::class,
            $user,
            ['action' => $this->generateUrl('profile_edit')]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->save($user);
            $this->addFlash('success', $this->translator->trans('message.updated_successfully'));

            return $this->redirectToRoute('profile_index');
        }

        return $this->render('profile/edit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Change password.
     *
     * @param Request $request Request
     *
     * @return Response Change password
     */
    #[Route('/password', name: 'profile_password', methods: ['GET', 'POST'])]
    public function changePassword(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(
            ChangePasswordType::class,
            null,
            ['action' => $this->generateUrl('profile_password')]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $newPassword */
            $newPassword = $form->get('newPassword')->getData();
            $this->userService->changePassword($user, $newPassword);
            $this->addFlash('success', $this->translator->trans('message.password_changed'));

            return $this->redirectToRoute('profile_index');
        }

        return $this->render('profile/password.html.twig', ['form' => $form->createView()]);
    }
}
