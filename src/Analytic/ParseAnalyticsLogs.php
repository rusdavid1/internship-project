<?php

declare(strict_types=1);

namespace App\Analytic;

use App\Login\LoginAttempt;
use App\Login\LoginCollection;
use Symfony\Component\Serializer\SerializerInterface;

class ParseAnalyticsLogs
{
    private SerializerInterface $serializer;

    private string $analyticsLogPath;

    public function __construct(SerializerInterface $serializer, string $analyticsLogPath)
    {
        $this->serializer = $serializer;
        $this->analyticsLogPath = $analyticsLogPath;
    }

    public function getLoginAttempts(): LoginCollection
    {
        $fp = new \SplFileObject($this->analyticsLogPath, 'r', true);

        $handler = $fp->openFile();
        $collection = new LoginCollection();

        while ($line = $handler->fgets()) {
            $loginAttempt = $this->serializer->deserialize($line, LoginAttempt::class, 'json');

            $collection->add($loginAttempt);
        }

        return $collection;
    }
}
