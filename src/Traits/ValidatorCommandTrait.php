<?php

namespace App\Traits;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

trait ValidatorCommandTrait
{
    public function displayErrorsInCli(
        ConstraintViolationListInterface $violationList,
        SymfonyStyle $io,
        int $returnCode
    ): int {
        foreach ($violationList as $violation) {
            /**
             * @var ConstraintViolation $error
             */

            $io->error($violation);
        }

        return $returnCode;
    }
}
