<?php

namespace Tests;

use Laravel\Forge\Resources\PHPVersion;
use Laravel\Forge\Resources\Server;
use Laravel\Forge\Resources\Site;
use Laravel\Forge\Resources\Worker;

trait FakesForgeResources
{
    protected array $forgeServers = [];
    protected array $forgeSites = [];
    protected array $forgePhpVersions = [];
    protected array $forgeWorkers = [];

    protected function withForgeServer(array $attributes = []): static
    {
        $this->forgeServers[] = new Server(array_merge([
            'id' => 1,
        ], $attributes));

        return $this;
    }

    protected function withForgeSite(array $attributes = []): static
    {
        $this->forgeSites[] = new Site(array_merge([
            'id' => 1,
        ], $attributes));

        return $this;
    }

    protected function withForgePhpVersion(array $attributes = []): static
    {
        $this->forgePhpVersions[] = new PHPVersion(array_merge([
            'id' => 1,
            'version' => 'php80',
            'used_as_default' => true,
            'used_on_cli' => true,
        ], $attributes));

        return $this;
    }

    protected function withForgeWorker(array $attributes = []): static
    {
        $this->forgeWorkers[] = new Worker(array_merge([
            'id' => 1,
            'command' => 'php8.0 /home/forge',
            'queue' => 'default',
            'connection' => 'redis',
            'daemon' => 0,
            'php' => 'php80',
            'timeout' => 60,
            'processes' => 1,
            'sleep' => 10,
            'delay' => 0,
            'tries' => null,
            'environment' => null,
            'force' => 0,
        ], $attributes));

        return $this;
    }
}
