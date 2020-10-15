<?php

namespace App\Commands;

use App\Support\Configuration;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Forge\Forge;
use LaravelZero\Framework\Commands\Command;

class PullConfigCommand extends ForgeCommand
{
    protected $signature = 'config:pull {environment=production}';

    protected $description = 'Pull the configuration from Laravel Forge and store it in your forge.yml file';

    /**
     * @param Forge $forge
     * @param Configuration $configuration
     * @return int
     */
    public function handle(Forge $forge, Configuration $configuration)
    {
        if (! $this->ensureHasToken()) {
            return 1;
        }
        if (! $this->ensureHasForgeConfiguration()) {
            return 1;
        }

        $environment = $this->argument('environment');

        $server = $forge->server($configuration->get($environment, 'server'));
        $site = $forge->site($server->id, $configuration->get($environment, 'id'));

        $configuration->initialize($environment, $server, $site, getcwd());

        $this->info('Successfully updated the Forge configuration file.');
    }
}
