<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class UserAgents extends BaseConfig
{
    /** @var array<string, string> */
    public array $platforms = [
        'windows nt 10.0' => 'Windows 10',
        'windows nt 6.3'  => 'Windows 8.1',
        'windows nt 6.2'  => 'Windows 8',
        'windows nt 6.1'  => 'Windows 7',
        'android'         => 'Android',
        'iphone'          => 'iOS',
        'ipad'            => 'iOS',
        'mac os x'        => 'Mac OS X',
        'linux'           => 'Linux',
    ];

    /** @var array<string, string> */
    public array $browsers = [
        'OPR'     => 'Opera',
        'Flock'   => 'Flock',
        'Edge'    => 'Edge',
        'Chrome'  => 'Chrome',
        'Safari'  => 'Safari',
        'Firefox' => 'Firefox',
        'MSIE'    => 'Internet Explorer',
    ];

    /** @var array<string, string> */
    public array $mobiles = [
        'mobileexplorer' => 'Mobile Explorer',
        'android'        => 'Android',
        'iphone'         => 'iPhone',
        'ipad'           => 'iPad',
        'ipod'           => 'iPod',
        'blackberry'     => 'BlackBerry',
        'webos'          => 'webOS',
    ];

    /** @var array<string, string> */
    public array $robots = [
        'googlebot'      => 'Googlebot',
        'msnbot'         => 'MSNBot',
        'bingbot'        => 'Bing',
        'slurp'          => 'Yahoo',
        'duckduckbot'    => 'DuckDuckGo',
        'baiduspider'    => 'Baidu',
        'yandexbot'      => 'Yandex',
        'facebookbot'    => 'Facebook',
    ];
}
