<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Debug\ExceptionHandler;
use CodeIgniter\Debug\ExceptionHandlerInterface;
use Psr\Log\LogLevel;
use Throwable;

class Exceptions extends BaseConfig
{
    /**
     * If true, then exceptions will be logged through Services::Log.
     */
    public bool $log = true;

    /**
     * Any status codes here will NOT be logged if logging is turned on.
     */
    public array $ignoreCodes = [404];

    /**
     * Path to the directory containing CLI and HTML error views.
     */
    public string $errorViewPath = APPPATH . 'Views/errors';

    /**
     * Data keys that should be hidden from debug traces.
     */
    public array $sensitiveDataInTrace = [];

    /**
     * Whether deprecated errors should be logged instead of throwing exceptions.
     */
    public bool $logDeprecations = true;

    /**
     * Log level used when logging deprecations.
     */
    public string $deprecationLogLevel = LogLevel::WARNING;

    /**
     * Returns the exception handler for a given status code.
     */
    public function handler(int $statusCode, Throwable $exception): ExceptionHandlerInterface
    {
        return new ExceptionHandler($this);
    }
}
