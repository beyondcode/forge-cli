<?php

namespace App\Commands;

use Laravel\Forge\Forge;
use App\Support\Configuration;

class PullEnvCommand extends ForgeCommand
{

    protected $signature = 'env:pull';

    protected $description = 'Pull the env file from Forge';

    /**
     * @param Forge $forge
     * @param Configuration $configuration
     */
    public function handle(Forge $forge, Configuration $configuration)
    {
        if (! $this->ensureHasToken()) {
            return 1;
        }
        if (! $this->ensureHasForgeConfiguration()) {
            return 1;
        }

        $env = $forge->siteEnvironmentFile($configuration->get('server'), $configuration->get('id'));

        file_put_contents('.env.forge', $env);

        $this->info('Wrote environment file to .env.forge');
    }
}
