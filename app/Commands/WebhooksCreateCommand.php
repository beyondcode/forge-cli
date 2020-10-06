<?php

namespace App\Commands;

use App\Support\Configuration;
use Laravel\Forge\Exceptions\ValidationException;
use Laravel\Forge\Forge;

class WebhooksCreateCommand extends ForgeCommand
{
    protected $signature = 'webhooks:create {url : Webhook URL}';

    protected $description = 'Create a deployment webhook on the current site';

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

        try {
            $webhook = $forge->createWebhook($serverId, $siteId, [
                'url' => $this->argument('url'),
            ]);
        } catch (ValidationException $e) {
            $this->error($e->errors['url'][0]);

            return 1;
        }

        $this->info("Deployment webhook {$webhook->id} created for URL: {$webhook->url}");
    }
}
