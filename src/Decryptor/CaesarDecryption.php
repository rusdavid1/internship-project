<?php

declare(strict_types=1);

namespace App\Decryptor;

class CaesarDecryption
{
    public function cipher(string $ch, float $key): string
    {
        if (!ctype_alpha($ch)) {
            return $ch;
        }

        $offset = ord(ctype_upper($ch) ? 'A' : 'a');

        return chr((int)fmod(((ord($ch) + $key) - $offset), 26) + $offset);
    }

    public function encipher(string $input, int $key): string
    {
        $output = "";

        $inputArr = str_split($input);
        foreach ($inputArr as $ch) {
            $output .= $this->cipher($ch, $key);
        }

        return $output;
    }

    public function decipher(string $input, int $key): string
    {
        return $this->encipher($input, 26 - $key);
    }
}
