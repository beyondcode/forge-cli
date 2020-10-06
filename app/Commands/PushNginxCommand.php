<?php

namespace App\Commands;

use App\Support\Configuration;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Forge\Forge;
use LaravelZero\Framework\Commands\Command;

class PushNginxCommand extends ForgeCommand
{
    protected $signature = 'nginx:push';

    protected $description = 'Push the Nginx config file to Forge';

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

        if (!file_exists('nginx-forge.conf')) {
            $this->error('The nginx-forge.conf file does not exist.');
            exit();
        }

        $siteId = $configuration->get('id');

        try {
            $forge->updateSiteNginxFile($configuration->get('server'), $configuration->get('id'), file_get_contents('nginx-forge.conf'));

            $this->info('Successfully updated the Nginx configuration on Forge.');
        } catch (\Exception $e) {
            $this->error('Something went wrong: ');
            $this->error($e->getMessage());
        }
    }
}
