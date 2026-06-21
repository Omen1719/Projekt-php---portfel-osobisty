<?php

/*
 * This file is part of the Personal Wallet project.
 */

declare(strict_types=1);

namespace App\Command;

use App\Entity\Enum\UserRole;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Console command that creates a new user or updates an existing one's password.
 */
#[AsCommand(
    name: 'app:create-user',
    description: 'Creates a new user or updates the password of an existing one (admin by default).',
)]
class CreateUserCommand extends Command
{
    /**
     * Constructor.
     *
     * @param UserRepository              $userRepository User repository
     * @param UserPasswordHasherInterface $passwordHasher Password hasher
     */
    public function __construct(private readonly UserRepository $userRepository, private readonly UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
    }

    /**
     * Configure.
     */
    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'User email')
            ->addArgument('password', InputArgument::REQUIRED, 'Plain password')
            ->addOption('admin', null, InputOption::VALUE_NONE, 'Grant ROLE_ADMIN');
    }

    /**
     * Execute.
     *
     * @param InputInterface  $input  Input
     * @param OutputInterface $output Output
     *
     * @return int Execute
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = (string) $input->getArgument('email');
        $password = (string) $input->getArgument('password');

        $user = $this->userRepository->findOneBy(['email' => $email]) ?? new User();
        $user->setEmail($email);
        $user->setRoles($input->getOption('admin') ? [UserRole::ROLE_ADMIN->value] : [UserRole::ROLE_USER->value]);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));

        $this->userRepository->save($user);

        $io->success(sprintf('User "%s" has been saved.', $email));

        return Command::SUCCESS;
    }
}
