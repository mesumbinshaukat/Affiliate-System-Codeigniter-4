<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class DocTypes extends BaseConfig
{
    /**
     * Mapping of document extensions to MIME types used by the helper.
     * Pulled from the framework's defaults to satisfy helpers that expect this config.
     *
     * @var array<string, string>
     */
    public array $types = [
        'pdf'  => 'application/pdf',
        'doc'  => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'xls'  => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'ppt'  => 'application/vnd.ms-powerpoint',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'txt'  => 'text/plain',
        'csv'  => 'text/csv',
        'rtf'  => 'application/rtf',
        'zip'  => 'application/zip',
        'rar'  => 'application/x-rar-compressed',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
        'gif'  => 'image/gif',
        'bmp'  => 'image/bmp',
        'svg'  => 'image/svg+xml',
        'webp' => 'image/webp',
    ];
}
