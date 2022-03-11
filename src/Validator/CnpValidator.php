<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class CnpValidator extends ConstraintValidator
{

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof Cnp) {
            throw new UnexpectedTypeException($constraint, Cnp::class);
        }
//TODO Check for february

        $regEx = '/^([1-8]\d{2})(0[1-9]|1[0-2])(0[1-9]|1\d|2\d|3[0-1])(0[1-9]|1\d|2\d|3\d|4[0-5]|5[1-2])(\d|[1-9]\d|[1-9]\d\d\d)$/m';
        $regExResponse = preg_match_all($regEx, $value, $matches, PREG_SET_ORDER, 0);

        $cConstValidation = function () use ($value) {
            $CNPCONST = '279146358279';
            $cnpConst = str_split($CNPCONST);
            $cnpTrim = str_split(substr($value, 0, -1));
            $cConst = substr($value, -1);

            $accum = 0;
            for ($i = 0; $i < count($cnpTrim); $i++) {
                $accum += ($cnpConst[$i] * $cnpTrim[$i]);
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

        $this->context->buildViolation($constraint->message)->atPath('cnp')->addViolation();
    }
}
