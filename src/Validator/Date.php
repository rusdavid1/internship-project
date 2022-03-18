<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Date extends Constraint
{
    public $message = 'Invalid date. It can\'t be in the past.';
}
