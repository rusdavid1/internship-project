<?php

declare(strict_types=1);

namespace App\Analytics;

class NumberOfApiLoginsPerUser
{
    public function test()
    {
        $fp = new \SplFileObject(__DIR__ . '/analytics.log', 'r');

        $handler = $fp->openFile();

        while ($line = $handler->fgets()) {
            echo $line;
            if (empty($line)) {
                    $handler = null;

                    break;
            }
        }
        return 'opasa';
    }

}
