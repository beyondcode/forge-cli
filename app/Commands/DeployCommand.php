<?php

namespace App\Commands;

use App\Support\Configuration;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Forge\Forge;
use LaravelZero\Framework\Commands\Command;

class DeployCommand extends ForgeCommand
{
    /** @var string */
    protected $signature = 'deploy {environment=production} {--update-script}';

    /** @var string */
    protected $description = 'Deploy the current project to Forge';

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

        if ($this->option('update-script')) {
            $this->info('Updating deployment script...');

            $script = implode("\n", $configuration->get($environment, 'deployment', []));

            $forge->updateSiteDeploymentScript($serverId, $siteId, $script);
        }

        $this->info("Deploying site on {$environment}...");

        $forge->deploySite($serverId, $siteId);

        $this->info('The site has been deployed');

        $this->call('deploy:log', [
            'environment' => $environment,
        ]);
    }
}
