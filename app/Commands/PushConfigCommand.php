<?php

namespace App\Commands;

use App\Support\Configuration;
use App\Sync\BaseSync;
use App\Sync\DaemonSync;
use App\Sync\DeploymentScriptSync;
use App\Sync\WebhookSync;
use App\Sync\WorkerSync;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Arr;
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
        WorkerSync::class,
    ];

    protected $signature = 'config:push {environment=production} {--sync=all} {--force}';

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

        $syncClasses = $this->option('sync') === 'all'
            ? static::SYNC_CLASSES
            : array_values(Arr::only([
                'webhooks' => WebhookSync::class,
                'deployment' => DeploymentScriptSync::class,
                'daemons' => DaemonSync::class,
                'workers' => WorkerSync::class,
            ], explode(',', $this->option('sync'))));

        $this->synchronize($environment, $server, $site, $syncClasses);

        $this->info('Done');
    }

    protected function synchronize(string $environment, Server $server, Site $site, array $syncClasses)
    {
        foreach ($syncClasses as $syncClass) {
            $this->info('Synchronizing ' . $syncClass);

            $output = fn (string $contents, string $level = 'info') => $this->{$level}($contents);

            /** @var BaseSync $synchronizer */
            $syncer = app($syncClass);
            $syncer->sync($environment, $server, $site, $output, $this->option('force'));
        }
    }
}
