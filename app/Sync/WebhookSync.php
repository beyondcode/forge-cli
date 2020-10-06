<?php

namespace App\Sync;

use Illuminate\Console\OutputStyle;
use Laravel\Forge\Resources\Server;
use Laravel\Forge\Resources\Site;
use Laravel\Forge\Resources\Webhook;

class WebhookSync extends BaseSync
{

    public function sync(Server $server, Site $site, OutputStyle $output, bool $force = false): void
    {
        $webhooks = collect($this->config->get('webhooks', []));
        $webhooksOnForge = $this->forge->webhooks($server->id, $site->id);

        // Create webhooks not on Forge
        $webhooks->diff(collect($webhooksOnForge)->map(function (Webhook $webhook) {
            return $webhook->url;
        }))->map(function ($url) use ($server, $site, $output) {
            $output->writeln("Creating webhook: {$url}");
            $this->forge->createWebhook($server->id, $site->id, [
                'url' => $url,
            ]);
        });

        // Delete webhooks on Forge but removed locally
        $deleteWebhooks = collect($webhooksOnForge)
            ->reject(function (Webhook $webhook) use ($webhooks) {
                return $webhooks->contains($webhook->url);
            });

        if (!$force && $deleteWebhooks->isNotEmpty()) {
            $output->warning("Skipping the deletion of {$deleteWebhooks->count()} Webhooks. \nUse --force to delete them.");
            return;
        }

        $deleteWebhooks->map(function (Webhook $webhook) use ($server, $site, $output) {
            $output->writeln("Deleting webhook: {$webhook->url}");
            $this->forge->deleteWebhook($server->id, $site->id, $webhook->id);
        });
    }
}
