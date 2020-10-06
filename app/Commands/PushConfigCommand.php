<?php

namespace App\Commands;

use App\Support\Configuration;
use App\Sync\BaseSync;
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
    ];

    protected $signature = 'config:push {--force}';

    protected $description = 'Push the configuration from your forge.yml file to Laravel Forge';

    /**
     * @param Forge $forge
     * @param Configuration $configuration
     * @return int
     */
    public function handle(Forge $forge, Configuration $configuration)
    {
        if (!$this->ensureHasToken()) {
            return 1;
        }
        if (!$this->ensureHasForgeConfiguration()) {
            return 1;
        }

        $server = $forge->server($configuration->get('server'));
        $site = $forge->site($server->id, $configuration->get('id'));

        $this->synchronize($server, $site);

        $this->info('Done');
    }

    protected function synchronize(Server $server, Site $site)
    {
        foreach (static::SYNC_CLASSES as $syncClass) {
            $this->info('Synchronizing ' . $syncClass);

            /** @var BaseSync $synchronizer */
            $syncer = app($syncClass);
            $syncer->sync($server, $site, $this->getOutput(), $this->option('force'));
        }
    }
}
