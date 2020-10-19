<?php

namespace App\Commands;

use App\Support\Configuration;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Arr;
use Laravel\Forge\Forge;
use LaravelZero\Framework\Commands\Command;

class SeeLogCommand extends ForgeCommand
{
    protected $signature = 'logs {--file=nginx_error} {environment=production}';

    protected $description = "Get server logs.\n\n  Possible file types: \n\n  - nginx_access, \n  - nginx_error, \n  - database, \n  - php7x (where x is a valid version number)";

    public function handle(Forge $forge, Configuration $configuration)
    {
        if (! $this->ensureHasToken()) {
            return 1;
        }
        if (! $this->ensureHasForgeConfiguration()) {
            return 1;
        }

        $environment = $this->argument('environment');

        $serverId = $configuration->get($environment, 'server');
        $siteId = $configuration->get($environment, 'id');

        $logs = $forge->get("servers/{$serverId}/logs?file=".$this->option('file'));

        $this->info('Log file: '.Arr::get($logs, 'path'));
        $this->info(Arr::get($logs, 'content'));
    }

}
