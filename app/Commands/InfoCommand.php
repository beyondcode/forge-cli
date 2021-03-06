<?php

namespace App\Commands;

use App\Support\Configuration;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Forge\Forge;

class InfoCommand extends ForgeCommand
{
    protected $signature = 'info {environment=production}';

    protected $description = 'Get information about the currently linked site on Forge.';

    public function handle(Forge $forge, Configuration $configuration)
    {
        $environment = $this->argument('environment');

        $serverId = $configuration->get($environment, 'server');
        $siteId = $configuration->get($environment, 'id');

        $server = $forge->server($serverId);
        $site = $forge->site($serverId, $siteId);

        $data = [
            ['Server', $server->name],
            ['IP', $server->ipAddress],
            ['Site', $site->name],
            ['Directory', $site->directory],
        ];

        $this->table(['Key', 'Value'], $data);
    }
}
