<?php

namespace Tests;

use Illuminate\Support\Collection;
use Laravel\Forge\Forge;
use Laravel\Forge\Resources\PHPVersion;
use Laravel\Forge\Resources\Server;
use Laravel\Forge\Resources\Site;
use Laravel\Forge\Resources\Worker;

trait FakesForgeResources
{
    protected $forge;

    protected Collection $forgeServers;
    protected Collection $forgeSites;
    protected Collection $forgePhpVersions;
    protected Collection $forgeWorkers;

    protected function mockForge(): void
    {
        foreach (['Servers', 'Sites', 'PhpVersions', 'Workers'] as $resource) {
            $this->{"forge{$resource}"} = new Collection;
        }

        $this->forge = $this->mock(Forge::class);

        $this->forge->shouldReceive('server')->andReturnUsing(
            fn ($id) => $this->forgeServers->firstWhere('id', $id)
        );
        $this->forge->shouldReceive('site')->andReturnUsing(
            fn ($server, $site) => $this->forgeSites->firstWhere('id', $site)
        );
        $this->forge->shouldReceive('phpVersions')->andReturnUsing(
            fn ($server) => $this->forgePhpVersions->toArray() // @todo differentiate by server?
        );
        $this->forge->shouldReceive('workers')->andReturnUsing(
            fn ($server, $site) => $this->forgeWorkers->toArray() // @todo differentiate by server?
        );
        $this->forge->shouldReceive('siteDeploymentScript')->andReturnUsing(
            fn ($server, $site) => ''
        );
        $this->forge->shouldReceive('webhooks')->andReturnUsing(
            fn ($server, $site) => []
        );
        $this->forge->shouldReceive('daemons')->andReturnUsing(
            fn ($server) => []
        );
    }

    protected function withForgeServer(array $attributes = []): static
    {
        $this->forgeServers->push(
            $this->app->make(Server::class, [
                'attributes' => array_merge([
                    'id' => $this->forgeServers->count() + 1,
                ], $attributes),
            ])
        );

        return $this;
    }

    protected function withForgeSite(array $attributes = []): static
    {
        $this->forgeSites->push(
            $this->app->make(Site::class, [
                'attributes' => array_merge([
                    'id' => $this->forgeSites->count() + 1,
                    'server_id' => $this->forgeServers->last()->id,
                ], $attributes),
            ])
        );

        return $this;
    }

    protected function withForgePhpVersion(array $attributes = []): static
    {
        $this->forgePhpVersions->push(
            $this->app->make(PHPVersion::class, [
                'attributes' => array_merge([
                    'id' => $this->forgePhpVersions->count() + 1,
                    'version' => 'php80',
                    'used_as_default' => false,
                    'used_on_cli' => false,
                ], $attributes),
            ])
        );

        return $this;
    }

    protected function withForgeWorker(array $attributes = []): static
    {
        $this->forgeWorkers->push(
            $this->app->make(Worker::class, [
                'attributes' => array_merge([
                    'id' => $this->forgeWorkers->count() + 1,
                    'command' => 'php8.0 /home/forge',
                    'queue' => 'default',
                    'connection' => 'redis',
                    'daemon' => 0,
                    'timeout' => 60,
                    'processes' => 1,
                    'sleep' => 10,
                    'delay' => 0,
                    'tries' => null,
                    'environment' => null,
                    'force' => 0,
                ], $attributes),
            ])
        );

        return $this;
    }
}
