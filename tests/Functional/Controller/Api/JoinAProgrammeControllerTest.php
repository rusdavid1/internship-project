<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Admin;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class JoinAProgrammeControllerTest extends WebTestCase
{
    public function testJoiningAProgramme()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('abcdslk@email.com');

        $client->request('POST', '/api/login', [
            'username' => $username,
            'password' => 'abcds@Aaa',
        ]);
    }
}