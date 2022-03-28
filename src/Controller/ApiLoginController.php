<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

class ApiLoginController
{
    /**
     * @Route (path="/api/login", name="api_programmes_login", methods={"POST"})
     */
    public function test(?User $user): Response
    {
        if (null === $user) {
            return new JsonResponse(['message' => 'missing credentials'], Response::HTTP_UNAUTHORIZED);
        }

        $token = Uuid::v4();

        return new JsonResponse($token, Response::HTTP_OK);
    }
}
