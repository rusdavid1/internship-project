<?php

declare(strict_types=1);

namespace App\Analytics;

use Symfony\Component\Serializer\SerializerInterface;

class ParseAnalyticsLogs
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function getLoginAttempts(): LoginCollection
    {
        $fp = new \SplFileObject(__DIR__ . '/analytics.log', 'r');

        $handler = $fp->openFile();
        $collection = new LoginCollection();

        while ($line = $handler->fgets()) {
            $loginAttempt = $this->serializer->deserialize($line, LoginAttempt::class, 'json');

            $collection->add($loginAttempt);
        }

        return $collection;
    }
}
