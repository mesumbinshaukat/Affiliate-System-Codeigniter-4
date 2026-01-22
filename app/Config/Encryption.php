<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Encryption extends BaseConfig
{
    /**
     * The driver to use for encryption.
     */
    public $driver = 'OpenSSL';

    /**
     * Encryption key. Define ENCRYPTION_KEY or encryption.key in your .env file
     * (e.g. encryption.key="base64:...generated key...").
     */
    public $key = '';

    /**
     * The block size in bytes.
     */
    public $blockSize = 16;

    /**
     * Digest algorithm for HMAC authentication.
     */
    public $digest = 'SHA512';

    public function __construct()
    {
        $this->key = env('encryption.key', getenv('ENCRYPTION_KEY') ?: '');
    }
}
