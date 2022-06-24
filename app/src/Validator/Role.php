<?php

namespace App\Validator;

use App\Entity\Role as EntityRole;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Role extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public string $message = 'The role "{{ value }}" is not valid.';
    public string $invalidFormatMessage = 'role: The role is not in valid format.';
    public string $notExistingMessage = 'role: The role does not exist.';

    /**
     * @return string
     */
    public function validatedBy(): string
    {
        return static::class . 'Validator';
    }
}
