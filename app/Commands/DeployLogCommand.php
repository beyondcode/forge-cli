<?php

namespace App\Commands;

use App\Support\Configuration;
use Laravel\Forge\Exceptions\NotFoundException;
use Laravel\Forge\Forge;

class DeployLogCommand extends ForgeCommand
{
    protected $signature = 'deploy:log {environment=production}';

    protected $description = 'View the latest deployment log';

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

        $this->info("Retrieving the latest deployment log on {$environment}...");

        try {
            $log = $forge->siteDeploymentLog($serverId, $siteId);
            $this->info('');
            $this->info('---------- BEGIN DEPLOYMENT LOG ----------');
            $this->line($log);
            $this->info('----------- END DEPLOYMENT LOG -----------');
        } catch (NotFoundException $exception) {
            $this->error("There is currently no deployment log available.");
        }
    }
}
