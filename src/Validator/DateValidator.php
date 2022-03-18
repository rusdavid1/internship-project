<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class DateValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Date) {
            throw new UnexpectedTypeException($constraint, Date::class);
        }

        $now = new \DateTime('now');
        $nowTimestamp = $now->getTimestamp();

        $dateTimestamp = $value->getTimestamp();

        if ($dateTimestamp > $nowTimestamp) {
            return;
        }

        $this->context->buildViolation($constraint->message)->addViolation();
    }
}
