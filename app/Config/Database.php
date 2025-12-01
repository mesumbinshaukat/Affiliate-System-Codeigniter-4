<?php

namespace Config;

use CodeIgniter\Database\Config;

class Database extends Config
{
    public string $filesPath = APPPATH . 'Database' . DIRECTORY_SEPARATOR;
    public string $defaultGroup = 'default';

    public array $default = [
        'DSN'          => '',
        'hostname'     => 'localhost',
        'username'     => 'root',
        'password'     => '',
        'database'     => 'lijstje_db',
        'DBDriver'     => 'MySQLi',
        'DBPrefix'     => '',
        'pConnect'     => false,
        'DBDebug'      => true,
        'charset'      => 'utf8mb4',
        'DBCollat'     => 'utf8mb4_general_ci',
        'swapPre'      => '',
        'encrypt'      => false,
        'compress'     => false,
        'strictOn'     => false,
        'failover'     => [],
        'port'         => 3306,
        'numberNative' => false,
    ];

    public function __construct()
    {
        parent::__construct();

        // Override with environment variables if set
        if (getenv('database.default.hostname') !== false) {
            $this->default['hostname'] = getenv('database.default.hostname');
        }
        if (getenv('database.default.database') !== false) {
            $this->default['database'] = getenv('database.default.database');
        }
        if (getenv('database.default.username') !== false) {
            $this->default['username'] = getenv('database.default.username');
        }
        if (getenv('database.default.password') !== false) {
            $this->default['password'] = getenv('database.default.password');
        }
        if (getenv('database.default.DBDriver') !== false) {
            $this->default['DBDriver'] = getenv('database.default.DBDriver');
        }
        if (getenv('database.default.port') !== false) {
            $this->default['port'] = (int) getenv('database.default.port');
        }
    }
}
