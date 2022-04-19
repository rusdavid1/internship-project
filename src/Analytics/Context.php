<?php

declare(strict_types=1);

namespace App\Analytics;

class Context
{
    private string $email;

    private string $loginType;

    private string $loginResult;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

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

    public function getLoginResult(): string
    {
        return $this->loginResult;
    }

    public function setLoginResult(string $loginResult): self
    {
        $this->loginResult = $loginResult;

        return $this;
    }
}
