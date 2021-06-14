<?php

namespace App\Support;

class Defaults
{
    public static function worker(string $php): array
    {
        return [
            'queue' => 'default', // Note: defaults to blank if omitted
            'connection' => 'redis', // Required by Forge API
            'php_version' => $php, // Required by Forge API
            'daemon' => false, // Required by Forge API
            'processes' => 1,
            'timeout' => 60, // Note: defaults to 0 (no timeout) if omitted
            'sleep' => 10, // Required by Forge API
            'delay' => 0,
            'tries' => null,
            'environment' => null,
            'force' => false,
        ];
    }
}
