<?php

namespace App\Commands;

use App\Support\Configuration;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Forge\Forge;
use LaravelZero\Framework\Commands\Command;

class PullNginxCommand extends ForgeCommand
{
    protected $signature = 'nginx:pull {environment=production}';

    protected $description = 'Pull the Nginx config file from Forge';

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
        $filename = "nginx-forge-{$environment}.conf";

        $config = $forge->siteNginxFile($configuration->get($environment, 'server'), $configuration->get($environment, 'id'));

        file_put_contents($filename, $config);

        $this->info('Wrote nginx config file to '.$filename);
    }
}
