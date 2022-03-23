<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class CnpValidator extends ConstraintValidator
{
    private const CNP_CONST = '279146358279';
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof Cnp) {
            throw new UnexpectedTypeException($constraint, Cnp::class);
        }
//TODO Check for february

        $regEx1 = '/^([1-8]\d{2})(0[1-9]|1[0-2])(0[1-9]|1\d|2\d|3[0-1])';
        $regEx2 = '(0[1-9]|1\d|2\d|3\d|4[0-5]|5[1-2])(\d|[1-9]\d|[1-9]\d{3})$/m';
        $regEx = $regEx1 . $regEx2;
        $regExResponse = preg_match_all($regEx, $value, $matches, PREG_SET_ORDER, 0);

        $cConstValidation = function () use ($value) {

            $cnpConst = str_split(self::CNP_CONST);
            $cnpTrim = str_split(substr($value, 0, -1));
            $cConst = substr($value, -1);

            $accum = 0;
            for ($i = 0; $i < count($cnpTrim); $i++) {
                $accum += (int)($cnpConst[$i] * (int)$cnpTrim[$i]);
            }

            if ($accum % 11 < 10) {
                return (int)$accum % 11 === (int)$cConst;
            }

            if ($accum % 11 === 10) {
                return 1 === (int)$cConst;
            }

            return false;
        };

        if ($regExResponse && $cConstValidation()) {
            return;
        }

        $this->context->buildViolation($constraint->message)->atPath('')->addViolation();
    }
}
