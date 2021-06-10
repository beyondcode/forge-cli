<?php

namespace App\Commands;

use Laravel\Forge\Forge;
use App\Support\Configuration;

class PullEnvCommand extends ForgeCommand
{

    protected $signature = 'env:pull {environment=production}';

    protected $description = 'Pull the env file from Forge';

    /**
     * @param Forge $forge
     * @param Configuration $configuration
     * @return int
     */
    public function handle(Forge $forge, Configuration $configuration)
    {
        $environment = $this->argument('environment');

        $env = $forge->siteEnvironmentFile($configuration->get($environment, 'server'), $configuration->get($environment, 'id'));

        file_put_contents(".env.forge.{$environment}", $env);

        $this->info("Wrote environment file to .env.forge.{$environment}");
    }
}
