<?php

declare(strict_types=1);

namespace App\Analytics;

use Exception;
use Traversable;

class LoginCollection implements \IteratorAggregate
{
    private array $loginAttempts;

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator();
    }

    public function add(LoginAttempt $loginAttempt): void
    {
        $this->loginAttempts[] = $loginAttempt;
    }

    public function getNumberOfSuccessfulLogins(): array
    {
        $table = [];

        foreach ($this->loginAttempts as $attempt) {
            if ($attempt->getLoginResult() === 'failed') {
                continue;
            }

            $loggedInUser = $attempt->getEmail();

            $table[] = $loggedInUser;
        }

        return $table;
    }

    public function getLoginAttempts(): array
    {
        return $this->loginAttempts;
    }
}
