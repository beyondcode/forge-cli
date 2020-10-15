<?php

namespace App\Commands;

use App\Support\Configuration;
use Laravel\Forge\Forge;

class PushEnvCommand extends ForgeCommand
{
    protected $signature = 'env:push {environment=production}';

    protected $description = 'Push the env file to Forge';

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

        $environment = $this->argument('environment');
        $envFile = ".env.forge.{$environment}";

        if (!file_exists($envFile)) {
            $this->error("The {$envFile} file does not exist.");
            exit();
        }


        $siteId = $configuration->get($environment, 'id');

        try {
            $forge->updateSiteEnvironmentFile(
                $configuration->get($environment, 'server'),
                $configuration->get($environment, 'id'),
                file_get_contents($envFile)
            );

            $this->info("Successfully updated the environment on Forge ({$environment}).");

            $shouldDeleteEnvFile = $this->confirm("Do you want to delete the local {$envFile} file?");

            if ($shouldDeleteEnvFile) {
                unlink($envFile);
            }
        } catch (\Exception $e) {
            $this->error('Something went wrong: ');
            $this->error($e->getMessage());
        }
    }
}
