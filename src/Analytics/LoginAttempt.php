<?php

declare(strict_types=1);

namespace App\Analytics;

class LoginAttempt
{
    private string $email;

    private \DateTime $dateTime;

    private Context $context;

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

    public function getContext(): Context
    {
        return $this->context;
    }

    public function setContext(Context $context): self
    {
        $this->context = $context;

        return $this;
    }
}
