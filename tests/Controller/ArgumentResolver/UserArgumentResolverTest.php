<?php

declare(strict_types=1);

namespace App\Tests\Controller\ArgumentResolver;

use App\Controller\Dto\UserDto;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Test\TestCase;
use App\Controller\ArgumentResolver\UserArgumentResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class UserArgumentResolverTest extends TestCase
{
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = new UserArgumentResolver();
    }


    public function testSupportUser()
    {
        $request = Request::create('/test');
        $argumentMetadata = new ArgumentMetadata('test', UserDto::class, true, true, true, true);
        $result = $this->user->supports($request, $argumentMetadata);

        self::assertNotFalse($result);
    }

    public function testResolveUser()
    {
        $request = Request::create(
            '/hello-world',
            'GET',
            [],
            [],
            [],
            [],
            json_encode(['firstName' => 'Fabien']),
        );
        $argumentMetadata = new ArgumentMetadata('test', UserDto::class, true, true, true, true);
        foreach ($this->user->resolve($request, $argumentMetadata) as $result) {
            $dto = $result;
        }

        $userDto = new UserDto();
        $userDto->firstName = 'Fabien';
        $userDto->lastName = 'rus';
        $userDto->password = '';
        $userDto->email = '';
        $userDto->cnp = '';

//        TODO More ArgumentResolver tests

//        self::assertIsIterable($result);
        self::assertEquals($dto, $result);
    }
}
