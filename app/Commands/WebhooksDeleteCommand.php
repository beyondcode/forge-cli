<?php

namespace App\Commands;

use App\Support\Configuration;
use Laravel\Forge\Exceptions\NotFoundException;
use Laravel\Forge\Forge;
use Laravel\Forge\Resources\Webhook;

class WebhooksDeleteCommand extends ForgeCommand
{
    protected $signature = "webhooks:delete
                            {id? : The ID of the deployment webhook to delete. If not provided then you will select the webhook.}";

    protected $description = 'Delete a deployment webhook from the current site';

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

        $webhookId = $this->argument('id');

        if ($webhookId) {
            try {
                $webhook = $forge->webhook($serverId, $siteId, $webhookId);
            } catch (NotFoundException $e) {
                $this->error('A deployment webhook with that ID was not found');

                return 1;
            }
        } else {
            $webhooks = $forge->webhooks($serverId, $siteId);

            if (! $webhooks) {
                $this->warn('No deployment webhooks found');

                return 1;
            }

            $selectedWebhook = $this->menu(
                'Which deployment webhook do you want to delete?',
                collect($webhooks)
                    ->map(function (Webhook $webhook) {
                        return "{$webhook->url} [{$webhook->id}]";
                    })
                    ->toArray()
            )->open();

            exit_if(is_null($selectedWebhook));

            $webhook = $webhooks[$selectedWebhook];
        }

        $webhook->delete();

        $this->info("Deployment webhook {$webhook->id} ({$webhook->url}) was deleted");
    }
}
