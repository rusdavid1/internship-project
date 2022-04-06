<?php

declare(strict_types=1);

namespace App\Tests\Controller\ArgumentResolver;

use App\Controller\Dto\UserDto;
use Monolog\Test\TestCase;
use App\Controller\ArgumentResolver\UserArgumentResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class UserArgumentResolverTest extends TestCase
{
    private $user;

    protected function setUp(): void
    {
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
            json_encode([
                'firstName' => 'Fabien',
                'lastName' => 'rus',
                'cnp' => '1234567890123',
                'password' => '6218746A@ds',
                'email' => 'abcd@fghek.com',
                ]),
        );
        $argumentMetadata = new ArgumentMetadata('test', UserDto::class, true, true, true, true);
        foreach ($this->user->resolve($request, $argumentMetadata) as $result) {
            $dto = $result;
        }

        $userDto = new UserDto();
        $userDto->firstName = 'Fabien';
        $userDto->lastName = 'rus';
        $userDto->password = '6218746A@ds';
        $userDto->email = 'abcd@fghek.com';
        $userDto->cnp = '1234567890123';

//        TODO More ArgumentResolver tests

//        self::assertIsIterable($result);
        self::assertEquals($dto, $result);
    }
}
