<?php

/*
 * This file is part of the Personal Wallet project.
 */

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Enum\UserRole;
use App\Entity\User;
use App\Form\Type\RegistrationFormType;
use App\Service\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * User registration controller.
 */
class RegistrationController extends AbstractController
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
     * Register.
     *
     * @param Request $request Request
     *
     * @return Response Register
     */
    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(Request $request): Response
    {
        if ($this->getUser() instanceof UserInterface) {
            return $this->redirectToRoute('home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles([UserRole::ROLE_USER->value]);
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();
            $this->userService->changePassword($user, $plainPassword);
            $this->addFlash('success', $this->translator->trans('message.registered'));

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', ['form' => $form->createView()]);
    }
}
