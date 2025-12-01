<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use DateTimeInterface;

class Cookie extends BaseConfig
{
    /**
     * Cookie Prefix
     */
    public string $prefix = '';

    /**
     * Cookie Expires Timestamp
     *
     * @var DateTimeInterface|int|string
     */
    public $expires = 0;

    /**
     * Cookie Path
     */
    public string $path = '/';

    /**
     * Cookie Domain
     */
    public string $domain = '';

    /**
     * Cookie Secure
     */
    public bool $secure = false;

    /**
     * Cookie HTTPOnly
     */
    public bool $httponly = true;

    /**
     * Cookie SameSite
     *
     * @var ''|'Lax'|'None'|'Strict'
     */
    public string $samesite = 'Lax';

    /**
     * Cookie Raw
     */
    public bool $raw = false;
}
