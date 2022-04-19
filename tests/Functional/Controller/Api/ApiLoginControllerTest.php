<?php

namespace App\Tests\Functional\Controller\Api;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @requires extension mysql
 */
class ApiLoginControllerTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $container = static::getContainer();
        $user = $container->get(UserRepository::class)->findOneBy(['id' => 5]);

        $client->jsonRequest('POST', '/api/login', [
            'username' => $user->email,
            'password' => $user->plainPassword,
        ]);

        $this->assertResponseIsSuccessful();
    }
}
