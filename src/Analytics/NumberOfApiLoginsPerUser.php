<?php

declare(strict_types=1);

namespace App\Analytics;

class NumberOfApiLoginsPerUser
{
    public function test()
    {
        $fp = new \SplFileObject(__DIR__ . '/analytics.log', 'r');

        $handler = $fp->openFile();

        $testArr = [];

        while ($line = $handler->fgets()) {
            $testArr[] = json_decode($line);

            if (empty($line)) {
                    $handler = null;
//        json decode

//                new failedloginInfo // implements interface getDate

//                push la collection de obj
                    break;
            }
        }
        return $testArr;
    }
}
