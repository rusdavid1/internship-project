<?php

declare(strict_types=1);

namespace App\Tests\Validator;

use App\Validator\Password;
use App\Validator\PasswordValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class PasswordValidatorTest extends ConstraintValidatorTestCase
{

    protected function createValidator()
    {
        return new PasswordValidator();
    }

    public function testPassword(): void
    {
        $password = '12312@A1';

        $result = $this->validator->validate($password, new Password());

        $this->buildViolation('Invalid password. Try a more complex one, like: 1234')->assertRaised();

        self::assertIsString($password);
        self::assertCount(8, str_split($password));
        self::assertContains('@', str_split($password));
    }
}
