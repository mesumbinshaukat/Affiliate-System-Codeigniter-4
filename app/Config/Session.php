<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Session\Handlers\BaseHandler;
use CodeIgniter\Session\Handlers\FileHandler;

class Session extends BaseConfig
{
    /**
     * Session Driver
     *
     * @var class-string<BaseHandler>
     */
    public string $driver = FileHandler::class;

    /**
     * Session Cookie Name
     */
    public string $cookieName = 'lijstje_session';

    /**
     * Session Expiration (in seconds)
     */
    public int $expiration = 7200;

    /**
     * Session Save Path
     */
    public string $savePath = WRITEPATH . 'session';

    /**
     * Session Match IP
     */
    public bool $matchIP = false;

    /**
     * Session Time to Update (in seconds)
     */
    public int $timeToUpdate = 300;

    /**
     * Session Regenerate Destroy
     */
    public bool $regenerateDestroy = false;

    /**
     * Session Database Group
     */
    public ?string $DBGroup = null;

    /**
     * Lock Retry Interval (microseconds) - for RedisHandler
     */
    public int $lockRetryInterval = 100_000;

    /**
     * Lock Max Retries - for RedisHandler
     */
    public int $lockMaxRetries = 300;
}
