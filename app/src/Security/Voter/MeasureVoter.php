<?php

namespace App\Security\Voter;

use App\Entity\Measure;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class MeasureVoter extends Voter
{
    public const MEASURE_EDIT = 'MEASURE_EDIT';
    public const MEASURE_READ = 'MEASURE_READ';
    public const MEASURE_DELETE = 'MEASURE_DELETE';

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::MEASURE_EDIT, self::MEASURE_READ, self::MEASURE_DELETE])
            && $subject instanceof \App\Entity\Measure;
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
            case self::MEASURE_EDIT:
                return $this->selfMeasure($user, $subject);
            case self::MEASURE_READ:
                return $this->selfMeasure($user, $subject);
            case self::MEASURE_DELETE:
                return $this->selfMeasure($user, $subject);
        }

        return false;
    }

    protected function selfMeasure(User $user, Measure $subject): bool
    {
        return $user->getId() === $subject->getUser()->getId();
    }
}
