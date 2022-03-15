<?php

declare(strict_types=1);

namespace App\Tests\Validator;

use App\Validator\Cnp;
use App\Validator\CnpValidator;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class CnpValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): ConstraintValidator
    {
        return new CnpValidator();
    }

    public function testCnpLength(): void
    {
        $cnp = '1234';
        $this->validator->validate($cnp, new Cnp());

        $this->buildViolation('This is not a valid CNP')->assertRaised();
    }

    public function cnpMocks(): array
    {
        return [
            ['76213562873691'],
            ['76213562873691'],
            ['4652146'],
            ['213124'],
            ['@A7861287637273'],
            ['@A7861287637273'],
        ];
    }

    /**
     * @dataProvider cnpMocks
     */
    public function testCnp(string $cnp): void
    {
        $this->validator->validate($cnp, new Cnp());

        $this->buildViolation('This is not a valid CNP')->assertRaised();
    }

    public function testNullCnpIsInvalid(): void
    {
        $this->validator->validate(null, new Cnp());

        $this->buildViolation('This is not a valid CNP')->assertRaised();
    }

    public function testEmptyCnpIsInvalid(): void
    {
        $this->validator->validate('', new Cnp());

        $this->buildViolation('This is not a valid CNP')->assertRaised();
    }

    public function testCnpIsValid(): void
    {
        $validCnp = '5021213132833';

        $this->validator->validate($validCnp, new Cnp());

        $this->assertNoViolation();
    }
}
