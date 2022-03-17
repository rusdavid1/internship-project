<?php

declare(strict_types=1);

namespace App\Command;

class CaesarDecryption
{

    public static function cipher($ch, $key)
    {
        if (!ctype_alpha($ch)) {
            return $ch;
        }

        $offset = ord(ctype_upper($ch) ? 'A' : 'a');
        return chr((int)fmod(((ord($ch) + $key) - $offset), 26) + $offset);
    }

    public static function encipher($input, $key): string
    {
        $output = "";

        $inputArr = str_split($input);
        foreach ($inputArr as $ch) {
            $output .= self::cipher($ch, $key);
        }

        return $output;
    }

    public static function decipher($input, $key)
    {
        return self::encipher($input, 26 - $key);
    }
}
