<?php

namespace App\Commands;

use App\Support\Configuration;
use Laravel\Forge\Forge;

class DeployResetCommand extends ForgeCommand
{
    protected $signature = 'deploy:reset';

    protected $description = "Reset the site's deployment state";

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

        $forge->resetDeploymentState($serverId, $siteId);

        $this->info('The deployment state has been reset');
    }
}
