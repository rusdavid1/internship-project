<?php

declare(strict_types=1);

namespace App\Tests\Unit\Controller\ArgumentResolver;

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

    public function testSupportUser(): void
    {
        $request = Request::create('/test');
        $argumentMetadata = new ArgumentMetadata('test', UserDto::class, true, true, true, true);
        $result = $this->user->supports($request, $argumentMetadata);

        self::assertNotFalse($result);
    }

    /**
     * @requires extension mysql
     */
    public function testResolveUser(): void
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
                'password' => '6218746A@dsAAA',
                'phoneNumber' => '6218746A@dsAAA',
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
        $userDto->password = '6218746A@dsAAA';
        $userDto->email = 'abcd@fghek.com';
        $userDto->cnp = '1234567890123';

        self::assertEquals($dto, $result);
    }
}
