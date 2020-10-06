<?php

namespace App\Commands;

use App\Support\Configuration;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Forge\Forge;
use LaravelZero\Framework\Commands\Command;

class PullNginxCommand extends ForgeCommand
{
    protected $signature = 'nginx:pull';

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

        $config = $forge->siteNginxFile($configuration->get('server'), $configuration->get('id'));

        file_put_contents('nginx-forge.conf', $config);

        $this->info('Wrote nginx config file to nginx-forge.conf');
    }
}
