<?php

namespace Tests;

use App\Support\Configuration;
use App\Sync\SyncWorkers;
use Laravel\Forge\Forge;
use Laravel\Forge\Resources\PHPVersion;
use Laravel\Forge\Resources\Server;
use Laravel\Forge\Resources\Site;
use Laravel\Forge\Resources\Worker;
use Mockery;
use stdClass;
use Tests\TestCase;

class SyncWorkersTest extends TestCase
{
    /** @test */
    public function can_diff_workers_found_in_local_config_and_on_forge()
    {
        $this->withConfig(<<<YAML
            production:
              id: 1
              server: 1
              workers:
                - queue: default
                  connection: redis
                  # daemon: true
            YAML);

        $this->mockForge();
        $this->withForgeServer()->withForgeSite()->withForgePhpVersion();
        $this->forge->expects()->server(1)->andReturns($this->forgeServers[0]);
        $this->forge->expects()->site(1, 1)->andReturns($this->forgeSites[0]);
        $this->forge->allows()->phpVersions(1)->andReturns($this->forgePhpVersions);

        $this->withForgeWorker();
        $this->forge->expects()->workers(1, 1)->andReturns($this->forgeWorkers);

        chdir(__DIR__ . DIRECTORY_SEPARATOR . 'fixtures');

        $this->artisan('config:push')
            ->expectsOutput('Done!')
            ->assertExitCode(0);
    }
}
