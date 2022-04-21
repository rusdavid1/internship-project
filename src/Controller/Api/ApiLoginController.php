<?php

declare(strict_types=1);

namespace App\Controller\Api;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Uid\Uuid;

class ApiLoginController implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private EntityManagerInterface $entityManager;

    private Security $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    /**
     * @Route (path="/api/login", name="api_programmes_login", methods={"POST"})
     */
    public function logInAction(): Response
    {
        $currentUser = $this->security->getUser();

        if (null === $currentUser) {
            return new JsonResponse(['message' => 'missing credentials'], Response::HTTP_UNAUTHORIZED);
        }

        $token = Uuid::v4();
        $currentUser->setApiToken($token);

        $this->logger->info('Successfully set the token');

        $this->entityManager->persist($currentUser);
        $this->entityManager->flush();

        return new Response('Successfully logged in', Response::HTTP_OK);
    }
}
