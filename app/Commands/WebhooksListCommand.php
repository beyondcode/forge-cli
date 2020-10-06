<?php

namespace App\Commands;

use App\Support\Configuration;
use Laravel\Forge\Forge;
use Laravel\Forge\Resources\Webhook;

class WebhooksListCommand extends ForgeCommand
{
    protected $signature = 'webhooks:list';

    protected $description = "List the current site's deployment webhooks";

    /**
     * @param Forge $forge
     * @param Configuration $configuration
     *
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

        $serverId = $configuration->get('server');
        $siteId = $configuration->get('id');

        $webhooks = collect($forge->webhooks($serverId, $siteId))
            ->map(function (Webhook $webhook) {
                return [$webhook->id, $webhook->url];
            });

        if ($webhooks) {
            $this->table(['ID', 'Webhook URL'], $webhooks);
        } else {
            $this->warn('No deployment webhooks found');
        }
    }
}
