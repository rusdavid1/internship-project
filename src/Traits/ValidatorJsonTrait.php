<?php

declare(strict_types=1);

namespace App\Traits;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolation;

trait ValidatorJsonTrait
{
    public function displayErrorsAsJson(array $errors): Response
    {
        $errorArr = [];
        foreach ($errors as $error) {
            /**
             * @var ConstraintViolation $error
             */
            $errorArr = [
                $error->getPropertyPath() => $error->getMessage()
            ];
        }

        return new JsonResponse($errorArr);
    }
}
