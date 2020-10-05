<?php

namespace App\Commands;

use App\Support\Configuration;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Forge\Forge;

class InfoCommand extends ForgeCommand
{
    /** @var string */
    protected $signature = 'info';

    /** @var string */
    protected $description = 'Get information about the currently linked site on Forge.';

    public function handle(Forge $forge, Configuration $configuration)
    {
        if (! $this->ensureHasToken()) {
            return 1;
        }
        if (! $this->ensureHasForgeConfiguration()) {
            return 1;
        }

        $serverId = $configuration->get('server');
        $siteId = $configuration->get('id');

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
