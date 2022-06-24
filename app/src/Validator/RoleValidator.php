<?php

namespace App\Validator;

use App\Repository\RoleRepository;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class RoleValidator extends ConstraintValidator
{
    private RoleRepository $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function validate($role, Constraint $constraint): void
    {
        /* @var App\Validator\Role $constraint */

        if (null === $role || '' === $role) {
            return;
        }

        if (empty((array)$role)) {
            /** @phpstan-ignore-next-line */
            $this->context->buildViolation($constraint->invalidFormatMessage)
                ->setParameter('{{ value }}', json_encode($role))
                ->addViolation();
            return;
        }

        /** @phpstan-ignore-next-line */
        $role = $this->roleRepository->findByLabel($role->getLabel());

        if (!$role) {
            /** @phpstan-ignore-next-line */
            $this->context->buildViolation($constraint->notExistingMessage)
                ->setParameter('{{ value }}', json_encode($role))
                ->addViolation();
        }
    }
}
