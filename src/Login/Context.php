<?php

declare(strict_types=1);

namespace App\Login;

class Context
{
    private string $email;

    private string $type;

    private string $result;

    private string $firewall;

    private string $role;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function setResult(string $result): self
    {
        $this->result = $result;

        return $this;
    }

    public function getFirewall(): string
    {
        return $this->firewall;
    }

    public function setFirewall(string $firewall): self
    {
        $this->firewall = $firewall;

        return $this;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }
}
