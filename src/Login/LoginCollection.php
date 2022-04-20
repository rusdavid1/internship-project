<?php

declare(strict_types=1);

namespace App\Login;

class LoginCollection
{
    private array $failedLoginAttempts;

    private array $apiLogins;

    private array $adminLogins;

    private array $adminLoginsPerDay;

    private array $newAccounts;

    public function add(LoginAttempt $loginAttempt): void
    {
        if ($loginAttempt->getContext()->result === 'failed') {
            $this->failedLoginAttempts[] = $loginAttempt;

            return;
        }

        if ($loginAttempt->getContext()->firewall === 'api') {
            $this->apiLogins[] = $loginAttempt;

            return;
        }

        if ($loginAttempt->getContext()->firewall === 'admin') {
            $this->adminLogins[] = $loginAttempt;

            return;
        }

        if ($loginAttempt->getContext()->type === 'register') {
            $this->newAccounts[] = $loginAttempt;
        }
    }

    public function getNumberOfApiLogins(): array
    {
        if (empty($this->apiLogins)) {
            return ['Not enough data'];
        }

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
        if (empty($this->adminLogins)) {
            return ['Not enough data'];
        }

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
        if (empty($this->newAccounts)) {
            return ['Not enough data'];
        }

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
        if (empty($this->failedLoginAttempts)) {
            return ['Not enough data'];
        }

        $failedLoginsPerDay = [];

        foreach ($this->failedLoginAttempts as $login) {
            $loginDay = $login->getDateTime()->format('d-m');

            $failedLoginsPerDay[$loginDay][] = $login->getContext()->getEmail();
        }

        $failedLoginsPerDayPerUser = [];

        foreach ($failedLoginsPerDay as $day => $failedLoginEmail) {
            $failedLoginsPerDayPerUser[$day] = array_count_values($failedLoginEmail);
        }

        return $failedLoginsPerDayPerUser;
    }
}
