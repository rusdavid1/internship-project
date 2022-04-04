<?php

declare(strict_types=1);

namespace App\Token;

use App\Repository\UserRepository;
use Symfony\Component\Uid\Uuid;

class ResetPasswordToken
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }


    public function validatingResetToken(Uuid $resetToken)
    {
        $forgottenUser = $this->userRepository->findOneBy(['resetToken' => $resetToken]);
        $userResetToken = $forgottenUser->getResetToken();

        if ($userResetToken->compare($resetToken)) {
            return 'Error';
        }

        $testTimestamp = $forgottenUser->getResetTokenCreatedAt()->getTimestamp();
        $expiredTimestamp = $testTimestamp + 900;
        $now = new \DateTime('now');
        $nowTimestamp = $now->getTimestamp();

        if ($nowTimestamp > $expiredTimestamp) {
            return 'Expired Link';
        }

        return $forgottenUser;
    }
}
