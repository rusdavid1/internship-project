<?php

declare(strict_types=1);

namespace App\Token;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Uid\Uuid;

class ResetPasswordToken
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function validatingResetToken(Uuid $resetToken): ?User
    {
        $forgottenUser = $this->userRepository->findOneBy(['resetToken' => $resetToken]);
        $userResetToken = $forgottenUser->getResetToken();

        if ($userResetToken->compare($resetToken)) {
            return null;
        }

        $testTimestamp = $forgottenUser->getResetTokenCreatedAt()->getTimestamp();
        $expiredTimestamp = $testTimestamp + 900;
        $now = new \DateTime('now');
        $nowTimestamp = $now->getTimestamp();

        if ($nowTimestamp > $expiredTimestamp) {
            return null;
        }

        return $forgottenUser;
    }
}
