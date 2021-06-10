<?php

namespace App\Commands;

use App\Support\Configuration;
use App\Sync\BaseSync;
use App\Sync\DaemonSync;
use App\Sync\DeploymentScriptSync;
use App\Sync\WebhookSync;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Forge\Forge;
use Laravel\Forge\Resources\Server;
use Laravel\Forge\Resources\Site;
use Laravel\Forge\Resources\Webhook;
use LaravelZero\Framework\Commands\Command;

class PushConfigCommand extends ForgeCommand
{
    const SYNC_CLASSES = [
        WebhookSync::class,
        DeploymentScriptSync::class,
        DaemonSync::class,
    ];

    protected $signature = 'config:push {environment=production} {--force}';

    protected $description = 'Push the configuration from your forge.yml file to Laravel Forge';

    /**
     * @param Forge $forge
     * @param Configuration $configuration
     * @return int
     */
    public function handle(Forge $forge, Configuration $configuration)
    {
        $environment = $this->argument('environment');

        $server = $forge->server($configuration->get($environment, 'server'));
        $site = $forge->site($server->id, $configuration->get($environment, 'id'));

        $this->synchronize($environment, $server, $site);

        $this->info('Done');
    }

    protected function synchronize(string $environment, Server $server, Site $site)
    {
        foreach (static::SYNC_CLASSES as $syncClass) {
            $this->info('Synchronizing ' . $syncClass);

            /** @var BaseSync $synchronizer */
            $syncer = app($syncClass);
            $syncer->sync($environment, $server, $site, $this->getOutput(), $this->option('force'));
        }
    }
}
