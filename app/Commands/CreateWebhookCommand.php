<?php

namespace App\Commands;

use App\Support\Configuration;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class CreateWebhookCommand extends ForgeCommand
{
    protected $signature = 'webhook {environment=production}';

    protected $description = 'Create a new Webhook on Laravel Forge';

    public function handle(Configuration $configuration)
    {
        $environment = $this->argument('environment');

        $url = $this->ask('Which webhook URL do you want to add');

        $webhooks = $configuration->get($environment, 'webhooks', []);

        $webhooks[] = $url;

        $configuration->set($environment, 'webhooks', $webhooks);

        $configuration->store(getcwd() . '/forge.yml');

        $this->info('Successfully stored the webhook in your forge.yml config file. You can push the configuration using "forge config:push".');
    }
}
