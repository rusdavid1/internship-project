<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Uid\Uuid;

class ApiLoginController
{
    private EntityManagerInterface $entityManager;

    private Security $security;


    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    /**
     * @Route (path="/api/login", name="api_programmes_login")
     */
    public function test(): Response
    {

        $currentUser = $this->security->getUser();
        $currentToken = $this->security->getToken();
        var_dump($currentToken->getRoleNames());

        var_dump($currentToken);
        var_dump($currentUser);
        if (null === $currentUser) {
            return new JsonResponse(['message' => 'missing credentials'], Response::HTTP_UNAUTHORIZED);
        }

        $token = Uuid::v4();
        $currentUser->setApiToken($token);
        $this->entityManager->persist($currentUser);
        $this->entityManager->flush();

        return new Response('Hello there');
    }
}
