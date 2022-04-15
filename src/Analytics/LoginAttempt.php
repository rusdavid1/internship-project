<?php

declare(strict_types=1);

namespace App\Analytics;

class LoginAttempt
{
    private string $email;

    private \DateTime $dateTime;

    private Context $context;

    private string $loginResult;

    private string $loginType;

    private int $loginCount = 0;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): LoginAttempt
    {
        $this->email = $email;

        return $this;
    }

    public function getDateTime(): \DateTime
    {
        return $this->dateTime;
    }

    public function setDateTime(\DateTime $dateTime): self
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    public function getLoginResult(): string
    {
        return $this->loginResult;
    }

    public function setLoginResult(string $loginResult): self
    {
        $this->loginResult = $loginResult;

        return $this;
    }

    public function getLoginType(): string
    {
        return $this->loginType;
    }

    public function setLoginType(string $loginType): self
    {
        $this->loginType = $loginType;

        return $this;
    }

    public function getLoginCount(): int
    {
        return $this->loginCount;
    }

    public function setLoginCount(int $loginCount): self
    {
        $this->loginCount = $loginCount;

        return $this;
    }
}
