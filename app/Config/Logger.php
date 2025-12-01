<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Logger extends BaseConfig
{
    /**
     * --------------------------------------------------------------------------
     * Error Logging Threshold
     * --------------------------------------------------------------------------
     *
     * You can enable error logging by setting a threshold over zero. The
     * threshold determines what gets logged. Any values below or equal to the
     * threshold will be logged.
     *
     * Threshold options are:
     *
     *  0 = Disables logging, Error logging TURNED OFF
     *  1 = Emergency Messages  - System is unusable
     *  2 = Alert Messages      - Action Must Be Taken Immediately
     *  3 = Critical Messages   - Application component unavailable, unexpected exception.
     *  4 = Runtime Errors      - Don't need immediate action, but should be monitored.
     *  5 = Warnings            - Exceptional occurrences that are not errors.
     *  6 = Notices             - Normal but significant events.
     *  7 = Info                - Interesting events, like user logging in, etc.
     *  8 = Debug               - Detailed debug information.
     *  9 = All Messages
     *
     * You can also pass an array with threshold levels to show individual error types
     *
     *  array(1, 2, 3, 8) = Emergency, Alert, Critical, and Debug messages
     *
     * For a live site you'll usually enable Critical or higher (3) to be logged otherwise
     * your log files will fill up very fast.
     */
    public int $threshold = 9;

    /**
     * --------------------------------------------------------------------------
     * Date Format for Logs
     * --------------------------------------------------------------------------
     *
     * Each item that is logged has an associated date. You can use PHP date
     * codes to set your own date formatting
     */
    public string $dateFormat = 'Y-m-d H:i:s';

    /**
     * --------------------------------------------------------------------------
     * Log Handlers
     * --------------------------------------------------------------------------
     *
     * The logging system supports multiple actions to be taken when something
     * is logged. This is done by allowing for multiple Handlers, special classes
     * designed to write the log to their chosen destinations, whether that is
     * a file on the getServer, a cloud-based service, or even taking actions such
     * as emailing the dev team.
     *
     * Each handler is defined by the class name used for that handler, and it
     * can have an array of configuration items that are passed to the constructor
     * of that class.
     */
    public array $handlers = [
        /*
         * --------------------------------------------------------------------
         * File Handler
         * --------------------------------------------------------------------
         */
        'CodeIgniter\Log\Handlers\FileHandler' => [
            'handles' => [
                'critical',
                'alert',
                'emergency',
                'debug',
                'error',
                'info',
                'notice',
                'warning',
            ],

            /*
             * The log levels that this handler will handle.
             */
            'path' => WRITEPATH . 'logs/',

            /*
             * The default filename extension for log files.
             * An extension of 'php' allows for protecting the log files via basic
             * scripting, when they are to be stored under a publicly accessible directory.
             *
             * Note: Leaving it blank will default to 'log'.
             */
            'fileExtension' => '',

            /*
             * The file system permissions to be applied on newly created log files.
             *
             * IMPORTANT: This MUST be an integer (no quotes) and you MUST use octal
             *            integer notation (i.e. 0700, 0644, etc.)
             */
            'filePermissions' => 0644,
        ],
    ];
}
