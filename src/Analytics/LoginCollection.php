<?php

declare(strict_types=1);

namespace App\Analytics;

class LoginCollection implements \IteratorAggregate
{
    private array $loginAttempts;

    private array $apiLogins;

    private array $adminLogins;

    private array $adminLoginsPerDay;

//    TODO Successful and failed buckets

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator();
    }

    public function add(LoginAttempt $loginAttempt): void
    {
        if ($loginAttempt->getContext()->getLoginType() === 'api') {
            $this->apiLogins[] = $loginAttempt;

            return;
        }

        if ($loginAttempt->getContext()->getLoginType() === 'admin') {
            $this->adminLogins[] = $loginAttempt;
        }
    }

    public function getLoginAttempts(): array
    {
        return $this->loginAttempts;
    }

    public function getNumberOfApiLogins(): array
    {
        $emailArr = [];

        foreach ($this->apiLogins as $login) {
            $emailArr[] = $login->getContext()->getEmail();
        }

        $numberOfLoginsPerUser = array_count_values($emailArr);
        arsort($numberOfLoginsPerUser);

        return $numberOfLoginsPerUser;
    }

    public function getNumberOfAdminLogins(): array
    {
        $numberOfLoginsPerDay = [];

        foreach ($this->adminLogins as $login) {
            $loginDay = $login->getDateTime()->format('d-m');
            $this->adminLoginsPerDay[$loginDay][] = $login;

            $numberOfLoginsPerDay[$loginDay]['count'] = count($this->adminLoginsPerDay[$loginDay]);
        }

        return $numberOfLoginsPerDay;
    }
}
