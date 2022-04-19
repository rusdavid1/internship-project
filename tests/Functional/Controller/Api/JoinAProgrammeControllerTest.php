<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Api;

use App\Repository\ProgrammeRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @requires extension mysql
 */
class JoinAProgrammeControllerTest extends WebTestCase
{
    public function testJoiningAProgramme()
    {
        $client = static::createClient();
        $container = static::getContainer();
        $userRepository = $container->get(UserRepository::class);
        $programmeId = $container->get(ProgrammeRepository::class)->findOneBy(['id' => 2])->getId();

        $testUser = $userRepository->findOneByEmail('abcdslk1@email.com');

        $client->loginUser($testUser);
        $client->request('POST', "http://internship.local/api/programmes/join/$programmeId");

        $this->assertResponseIsSuccessful();
    }

    public function testFailingToJoinAProgrammeForbiddenAccess()
    {
        $client = static::createClient();
        $container = static::getContainer();
        $userRepository = $container->get(UserRepository::class);
        $programmeId = $container->get(ProgrammeRepository::class)->findOneBy(['id' => 2])->getId();

        $testUser = $userRepository->findOneByEmail('abcdslk0@email.com');

        $client->loginUser($testUser);
        $client->jsonRequest(
            'POST',
            "http://internship.local/api/programmes/join/$programmeId",
            [
                'id' => 2
            ]
        );

        $this->assertResponseStatusCodeSame(403);
    }

    public function testFailingToJoinAProgrammeNullUser()
    {
        $client = static::createClient();
        $container = static::getContainer();
        $userRepository = $container->get(UserRepository::class);
        $programmeId = $container->get(ProgrammeRepository::class)->findOneBy(['id' => 2])->getId();

        $testUser = $userRepository->findOneByEmail('abcdslk1@email.com');

        $client->loginUser($testUser);
        $client->jsonRequest(
            'POST',
            "http://internship.local/api/programmes/join/$programmeId",
            [
                'id' => 6
            ]
        );

        $this->assertResponseStatusCodeSame(404);
    }
}
