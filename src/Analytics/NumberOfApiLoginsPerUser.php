<?php

declare(strict_types=1);

namespace App\Analytics;

use Symfony\Component\Serializer\SerializerInterface;

class NumberOfApiLoginsPerUser
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function getLoginAttempts()
    {
        $fp = new \SplFileObject(__DIR__ . '/analytics.log', 'r');

        $handler = $fp->openFile();
        $collection = new LoginCollection();

        while ($line = $handler->fgets()) {
//            $log = json_decode($line);
//            $loginAttempt = new LoginAttempt();
//            $loginAttempt->setEmail($log->context->email);
//            $loginAttempt->setLoginResult($log->context->login_result);
//            $loginAttempt->setLoginType($log->context->login_type);
//            $loginAttempt->setDateTime(new \DateTime($log->datetime));

            $loginAttempt = $this->serializer->deserialize($line, LoginAttempt::class, 'json');

            $collection->add($loginAttempt);

        }

        return $collection;
    }
}
