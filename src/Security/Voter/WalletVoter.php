<?php

/*
 * This file is part of the Personal Wallet project.
 */

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Wallet;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, Wallet>
 */
class WalletVoter extends Voter
{
    public const VIEW = 'VIEW';
    public const EDIT = 'EDIT';
    public const DELETE = 'DELETE';

    /**
     * Supports.
     *
     * @param string $attribute Attribute
     * @param mixed  $subject   Subject
     *
     * @return bool Supports
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE], true)
            && $subject instanceof Wallet;
    }

    /**
     * Vote on attribute.
     *
     * @param string         $attribute Attribute
     * @param mixed          $subject   Subject
     * @param TokenInterface $token     Token
     * @param Vote|null      $vote      Vote
     *
     * @return bool Vote on attribute
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }
        if (!$subject instanceof Wallet) {
            return false;
        }

        return match ($attribute) {
            self::VIEW, self::EDIT, self::DELETE => $subject->getAuthor() === $user,
            default => false,
        };
    }
}
