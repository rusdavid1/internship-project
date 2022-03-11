<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class PasswordValidator extends ConstraintValidator
{

    public function validate($value, Constraint $constraint)
    {
        if(!$constraint instanceof Password) {
            throw new UnexpectedTypeException($constraint, Password::class);
        }

        $regEx = "/^(?=.*[A-Z])(?=.*[!@#$%^&*])[\w!@#$%^&*]{8,}$/m";
        $regExResponse = preg_match_all($regEx, $value, $matches, PREG_SET_ORDER, 0);

        if($regExResponse) return;

        $this->context->buildViolation($constraint->message)->atPath('password')->addViolation();
    }
}