<?php

declare(strict_types=1);

namespace App\Tests\Validator;

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
            ['1234iouf'],
            ['1234ouf'],
            ['1234io'],
            ['1234iouf'],
        ];
    }

    /**
     * @dataProvider passwordMocks
     */
    public function testValidate(string $password): void
    {
        $result = $this->validator->validate($password, new Password());

        $this->buildViolation('Invalid password. Try a more complex one, like: 1234')->assertRaised();
    }

    public function testValidPassword(): void
    {
        $validPass = '1234@Abcd';

        $this->validator->validate($validPass, new Password());

        $this->assertNoViolation();
    }
}
