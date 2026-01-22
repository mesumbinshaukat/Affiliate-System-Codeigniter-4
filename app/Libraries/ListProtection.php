<?php

namespace App\Libraries;

use Config\Services;

class ListProtection
{
    public static function encrypt(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $encrypter = Services::encrypter();
        return base64_encode($encrypter->encrypt($value));
    }

    public static function decrypt(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $encrypter = Services::encrypter();

        try {
            return $encrypter->decrypt(base64_decode($value, true) ?: '');
        } catch (\Throwable $e) {
            return null;
        }
    }

    public static function verify(string $input, ?string $encryptedValue): bool
    {
        $stored = static::decrypt($encryptedValue);
        if ($stored === null) {
            return false;
        }

        return hash_equals(trim($stored), trim($input));
    }
}

