<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    public const USER_READ = 'USER_READ';
    public const USER_EDIT = 'USER_EDIT';
    public const USER_DELETE = 'USER_DELETE';

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::USER_READ, self::USER_EDIT, self::USER_DELETE])
            && $subject instanceof \App\Entity\User;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        $role = $user->getRole()->getLabel();

        if ($role === 'admin') {
            return true;
        }

        switch ($attribute) {
            case self::USER_READ:
                return $this->selfUser($user, $subject);
            case self::USER_EDIT:
                return $this->selfUser($user, $subject);
            case self::USER_DELETE:
                return $this->selfUser($user, $subject);
        }

        return false;
    }

    protected function selfUser(User $user, User $subject): bool
    {
        return $user->getId() === $subject->getId();
    }
}
