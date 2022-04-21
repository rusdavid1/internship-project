<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Api;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    /**
     * @requires extension mysql
     */
    public function testDeletingAUser(): void
    {
        $client = static::createClient();
        $container = static::getContainer();
        $userRepository = $container->get(UserRepository::class);

        $testUser = $userRepository->findOneBy(['id' => 5]);
        $client->loginUser($testUser);

        $client->loginUser($testUser);
        $client->request('DELETE', "http://internship.local/api/users/5");

        $this->assertResponseIsSuccessful();
    }
}
