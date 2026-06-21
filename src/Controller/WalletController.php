<?php

/*
 * This file is part of the Personal Wallet project.
 */

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Entity\Wallet;
use App\Form\Type\WalletType;
use App\Security\Voter\WalletVoter;
use App\Service\WalletServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Controller for managing the current user's wallets.
 */
#[Route('/wallet')]
class WalletController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param WalletServiceInterface $walletService Wallet service
     * @param TranslatorInterface    $translator    Translator
     */
    public function __construct(private readonly WalletServiceInterface $walletService, private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Index.
     *
     * @param int $page Page
     *
     * @return Response Index
     */
    #[Route(name: 'wallet_index', methods: ['GET'])]
    public function index(#[MapQueryParameter] int $page = 1): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $pagination = $this->walletService->getPaginatedList($page, $user);

        return $this->render('wallet/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Create.
     *
     * @param Request $request Request
     *
     * @return Response Create
     */
    #[Route('/create', name: 'wallet_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $wallet = new Wallet();
        $form = $this->createForm(
            WalletType::class,
            $wallet,
            ['action' => $this->generateUrl('wallet_create')]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();
            $wallet->setAuthor($user);
            $this->walletService->save($wallet);
            $this->addFlash('success', $this->translator->trans('message.created_successfully'));

            return $this->redirectToRoute('wallet_index');
        }

        return $this->render('wallet/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * View.
     *
     * @param Wallet $wallet Wallet
     *
     * @return Response View
     */
    #[Route(
        '/{id}',
        name: 'wallet_view',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['GET']
    )]
    public function view(Wallet $wallet): Response
    {
        $this->denyAccessUnlessGranted(WalletVoter::VIEW, $wallet);

        return $this->render(
            'wallet/view.html.twig',
            [
                'wallet' => $wallet,
                'balance' => $this->walletService->getBalance($wallet),
                'can_be_deleted' => $this->walletService->canBeDeleted($wallet),
            ]
        );
    }

    /**
     * Edit.
     *
     * @param Request $request Request
     * @param Wallet  $wallet  Wallet
     *
     * @return Response Edit
     */
    #[Route(
        '/{id}/edit',
        name: 'wallet_edit',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['GET', 'PUT']
    )]
    public function edit(Request $request, Wallet $wallet): Response
    {
        $this->denyAccessUnlessGranted(WalletVoter::EDIT, $wallet);

        $form = $this->createForm(
            WalletType::class,
            $wallet,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('wallet_edit', ['id' => $wallet->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->walletService->save($wallet);
            $this->addFlash('success', $this->translator->trans('message.updated_successfully'));

            return $this->redirectToRoute('wallet_index');
        }

        return $this->render(
            'wallet/edit.html.twig',
            [
                'form' => $form->createView(),
                'wallet' => $wallet,
            ]
        );
    }

    /**
     * Delete.
     *
     * @param Request $request Request
     * @param Wallet  $wallet  Wallet
     *
     * @return Response Delete
     */
    #[Route(
        '/{id}/delete',
        name: 'wallet_delete',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['GET', 'DELETE']
    )]
    public function delete(Request $request, Wallet $wallet): Response
    {
        $this->denyAccessUnlessGranted(WalletVoter::DELETE, $wallet);

        if (!$this->walletService->canBeDeleted($wallet)) {
            $this->addFlash('warning', $this->translator->trans('message.wallet_contains_operations'));

            return $this->redirectToRoute('wallet_index');
        }

        $form = $this->createForm(
            FormType::class,
            $wallet,
            [
                'method' => 'DELETE',
                'action' => $this->generateUrl('wallet_delete', ['id' => $wallet->getId()]),
                'validation_groups' => false,
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->walletService->delete($wallet);
            $this->addFlash('success', $this->translator->trans('message.deleted_successfully'));

            return $this->redirectToRoute('wallet_index');
        }

        return $this->render(
            'wallet/delete.html.twig',
            [
                'form' => $form->createView(),
                'wallet' => $wallet,
            ]
        );
    }
}
