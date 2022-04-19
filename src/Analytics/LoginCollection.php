<?php

declare(strict_types=1);

namespace App\Analytics;

class LoginCollection implements \IteratorAggregate
{
    private array $failedLoginAttempts;

    private array $apiLogins;

    private array $adminLogins;

    private array $adminLoginsPerDay;

    private array $newAccounts;

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator();
    }

    public function add(LoginAttempt $loginAttempt): void
    {
//        $this->($this->lookupTable[$loginAttempt->getContext()->getLoginType()]) = $loginAttempt->getContext()->getLoginType();

//        array_push("$$this->lookupTable[$loginAttempt->getContext()->getLoginType()]", $loginAttempt->getContext()->getLoginType());

        if ($loginAttempt->getContext()->getLoginResult() === 'failed') {
            $this->failedLoginAttempts[] = $loginAttempt;

            return;
        }

        if ($loginAttempt->getContext()->getLoginType() === 'api') {
            $this->apiLogins[] = $loginAttempt;

            return;
        }

        if ($loginAttempt->getContext()->getLoginType() === 'admin') {
            $this->adminLogins[] = $loginAttempt;

            return;
        }

        if ($loginAttempt->getContext()->getLoginType() === 'registered') {
            $this->newAccounts[] = $loginAttempt;
        }
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

    public function getNewAccountsPercentage(): array
    {
        $roles = [];
        foreach ($this->newAccounts as $newAccount) {
            $roles[] = $newAccount->getContext()->getRole();
        }
        $totalRoles = count($roles);
        $rolesOccurrence = array_count_values($roles);

        $statistics = [];
        foreach ($rolesOccurrence as $role => $numberOfRoles) {
            $statistics[$role][] = ($numberOfRoles / $totalRoles) * 100 . '%';
        }

        return $statistics;
    }

    public function getFailedLoginsPerDay(): array
    {
        $failedLoginsPerDay = [];
        $failedLoginEmails = [];

        foreach ($this->failedLoginAttempts as $login) {
            $loginDay = $login->getDateTime()->format('d-m');
            $failedLoginEmails[$loginDay][$login->getContext()->getEmail()][] = $login->getContext()->getEmail();

            $failedLoginsPerDay[$loginDay][] = $login;
        }

        return $failedLoginsPerDay;
    }
}
