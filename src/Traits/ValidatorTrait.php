<?php

namespace App\Traits;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolation;

trait ValidatorTrait
{
    public function displayErrors($errors): Response
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
