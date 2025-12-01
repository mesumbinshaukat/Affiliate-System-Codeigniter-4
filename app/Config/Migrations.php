<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Migrations extends BaseConfig
{
    /**
     * Enable/Disable Migrations
     */
    public bool $enabled = true;

    /**
     * Migrations Table
     */
    public string $table = 'migrations';

    /**
     * Timestamp Format
     */
    public string $timestampFormat = 'Y-m-d-His_';
}
