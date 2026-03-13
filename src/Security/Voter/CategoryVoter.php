<?php

namespace App\Security\Voter;

use App\Entity\Category;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class CategoryVoter extends Voter
{
    public const WRITE = 'CATEGORY_WRITE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::WRITE && $subject instanceof Category;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        // Admin peut tout faire
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        // Sinon accès refusé
        return false;
    }
}
