<?php

namespace App\Commands;

use App\Support\Configuration;
use Laravel\Forge\Forge;

class DeployResetCommand extends ForgeCommand
{
    protected $signature = 'deploy:reset {environment=production}';

    protected $description = "Reset the site's deployment state";

    public function handle(Forge $forge, Configuration $configuration)
    {
        $environment = $this->argument('environment');

        $serverId = $configuration->get($environment, 'server');
        $siteId = $configuration->get($environment, 'id');

        $forge->resetDeploymentState($serverId, $siteId);

        $this->info('The deployment state has been reset');
    }
}
