<?php

declare(strict_types=1);

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Uid\Uuid;

class ApiLoginController
{
    private EntityManagerInterface $entityManager;

    private Security $security;

    private LoggerInterface $analyticsLogger;

    public function __construct(EntityManagerInterface $entityManager, Security $security, LoggerInterface $analyticsLogger)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->analyticsLogger = $analyticsLogger;
    }

    /**
     * @Route (path="/api/login", name="api_programmes_login", methods={"POST"})
     */
    public function logInAction(): Response
    {
        $currentUser = $this->security->getUser();

        if (null === $currentUser) {
            $this->analyticsLogger->warning('Missing credentials', ['user' => $currentUser]);

            return new JsonResponse(['message' => 'missing credentials'], Response::HTTP_UNAUTHORIZED);
        }

        $token = Uuid::v4();
        $currentUser->setApiToken($token);

        $this->entityManager->persist($currentUser);
        $this->entityManager->flush();

        $this->analyticsLogger->info('Successfully logged in', ['username' => $currentUser->email]);

        return new Response('Successfully logged in', Response::HTTP_OK);
    }
}
