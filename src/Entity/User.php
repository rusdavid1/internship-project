<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;

class User
{
    private int $id;

    private string $password;

    private string $cnp;

    public string $email = '';

    public string $firstName = '';

    public string $lastName = '';

    private Collection $roles;

    private Collection $programmes;

    public function getId(): int
    {
        return $this->id;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getCnp(): string
    {
        return $this->cnp;
    }

    public function setCnp(string $cnp): self
    {
        $this->cnp = $cnp;

        return $this;
    }

    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function setRoles(Collection $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getProgrammes(): Collection
    {
        return $this->programmes;
    }

    public function setProgrammes(Collection $programmes): self
    {
        $this->programmes = $programmes;

        return $this;
    }
}