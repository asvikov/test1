<?php

namespace App\Services;

class GenerateIdentityCodeService {

    public static function generateCode($length = 8) {

        $character = '1234567890QWERTYUIOPASDFGHJKLZXCVBNM';
        $character_length = strlen($character);
        $result = '';

        for($i = 0; $i < $length; $i++) {
            $random_character = $character[mt_rand(0, $character_length - 1)];
            $result .= $random_character;
        }

        return $result;
    }
}
