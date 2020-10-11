<?php

namespace App\Commands;

use App\Support\Configuration;
use Laravel\Forge\Forge;

class PushEnvCommand extends ForgeCommand
{
    protected $signature = 'env:push';

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

        if (!file_exists('.env.forge')) {
            $this->error('The .env.forge file does not exist.');
            exit();
        }

        $siteId = $configuration->get('id');

        try {
            $forge->updateSiteEnvironmentFile($configuration->get('server'), $configuration->get('id'), file_get_contents('.env.forge'));

            $this->info('Successfully updated the environment on Forge.');
        } catch (\Exception $e) {
            $this->error('Something went wrong: ');
            $this->error($e->getMessage());
        }
    }
}
