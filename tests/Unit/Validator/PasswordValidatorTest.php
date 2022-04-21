<?php

declare(strict_types=1);

namespace App\Tests\Unit\Validator;

use App\Entity\User;
use App\Validator\Password;
use App\Validator\PasswordValidator;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class PasswordValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): ConstraintValidator
    {
        return new PasswordValidator();
    }

    public function passwordMocks(): array
    {
        return [
            ['1234@Aiouf'],
            ['1234@Aiouf'],
            ['1234@Aio'],
            ['1234iouf'],
        ];
    }

    public function testValidPassword(): void
    {
        $validPass = '1234@Abcd';

        $this->validator->validate($validPass, new Password());

        $this->assertNoViolation();
    }
}
