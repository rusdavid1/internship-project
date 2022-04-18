<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiLoginControllerTest extends WebTestCase
{
    public function testSomething(): void
    {
        $username = 'abcdslk@email.com';

        $client = static::createClient();
        $client->request('POST', '/api/login', [
            'username' => $username,
            'password' => 'abcds@Aaa',
        ]);

        $this->assertResponseIsSuccessful();
        $decodedContent = json_decode($client->getResponse()->getContent(), true);
        $token = $decodedContent['token'];
        $usernameResponse = $decodedContent['username'];

        $crawler2 = $client->request('GET', 'http://internship.local/api/programmes', [], [], [
            'HTTP_X-AUTH-TOKEN' => $token,
            'HTTP_ACCEPT' => 'application/json'
        ]);

        $response = $client->getResponse()->getContent();
        $this->assertResponseIsSuccessful();
        $this->assertEquals($username, $usernameResponse);

    }
}
