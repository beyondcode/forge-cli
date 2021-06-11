<?php

namespace Tests;

use Laravel\Forge\Forge;
use Mockery;
use Tests\TestCase;

class SyncWorkersTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->mockForge();
    }

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

        $this->withForgeServer()
            ->withForgeSite()
            ->withForgePhpVersion(['used_on_cli' => true])
            ->withForgeWorker();

        // No additional Forge expectations because the local and Forge workers
        // are the same - the command should determine this and do nothing

        $this->inFixtureDir()->artisan('config:push')
            ->expectsOutput('Done!')
            ->assertExitCode(0);
    }

    public function syncDownWorkersProvider(): array
    {
        return [
            'omits default values' => [
                '', [
                    ['daemon' => 0, 'processes' => 1],
                ], <<<YAML
                  workers:
                    -
                      queue: default
                      connection: redis
                YAML,
            ],
            'one default worker' => [
                '', [
                    [],
                ], <<<YAML
                  workers:
                    -
                      queue: default
                      connection: redis
                YAML,
            ],
            'second worker on non-default queue' => [
                '', [
                    [],
                    ['queue' => 'emails'],
                ], <<<YAML
                  workers:
                    -
                      queue: default
                      connection: redis
                    -
                      queue: emails
                      connection: redis
                YAML,
            ],
            'two workers on non-default queues' => [
                '', [
                    ['queue' => 'emails', 'daemon' => 1, 'command' => 'php7.4 /home/forge'],
                    ['connection' => 'rabbitmq'],
                ], <<<YAML
                  workers:
                    -
                      queue: emails
                      connection: redis
                      php: php74
                      daemon: true
                    -
                      queue: default
                      connection: rabbitmq
                YAML,
            ],
            'non-default everything' => [
                '', [
                    [
                        'command' => 'php7.3 /home/forge',
                        'queue' => 'jobs',
                        'connection' => 'rabbit',
                        'daemon' => 1,
                        'timeout' => 0,
                        'processes' => 2,
                        'sleep' => 5,
                        'delay' => 1,
                        'tries' => 3,
                        'environment' => 'staging',
                        'force' => 1,
                    ],
                ], <<<YAML
                  workers:
                    -
                      queue: jobs
                      connection: rabbit
                      php: php73
                      daemon: true
                      processes: 2
                      timeout: 0
                      sleep: 5
                      delay: 1
                      tries: 3
                      environment: staging
                      force: true
                YAML,
            ],
            'two identical default workers' => [
                '', [
                    [],
                    [],
                ], <<<YAML
                  workers:
                    -
                      queue: default
                      connection: redis
                    -
                      queue: default
                      connection: redis
                YAML,
            ],
            'three non-default workers, two are identical' => [
                '', [
                    ['connection' => 'rabbit'],
                    ['connection' => 'reddit'],
                    ['connection' => 'rabbit'],
                ], <<<YAML
                  workers:
                    -
                      queue: default
                      connection: rabbit
                    -
                      queue: default
                      connection: reddit
                    -
                      queue: default
                      connection: rabbit
                YAML,
            ],
            'default worker already exists locally' => [
                <<<YAML
                workers:
                  -
                    queue: default
                    connection: redis
                YAML, [
                    [],
                    ['connection' => 'rabbit'],
                ], <<<YAML
                  workers:
                    -
                      queue: default
                      connection: redis
                    -
                      queue: default
                      connection: rabbit
                YAML,
            ],
            'all workers already exist locally' => [
                <<<YAML
                workers:
                  -
                    queue: jobs
                    connection: rabbit
                    php: php73
                    daemon: true
                  -
                    queue: default
                    connection: redis
                    timeout: 0
                    processes: 2
                    sleep: 5
                    delay: 1
                  -
                    queue: default
                    connection: redis
                    tries: 3
                    environment: staging
                    force: true
                  -
                    queue: default
                    connection: redis
                    php: php73
                YAML, [
                    ['command' => 'php7.3 /home/forge', 'queue' => 'jobs', 'connection' => 'rabbit', 'daemon' => 1],
                    ['timeout' => 0, 'processes' => 2, 'sleep' => 5, 'delay' => 1],
                    ['tries' => 3, 'environment' => 'staging', 'force' => 1],
                    ['command' => 'php7.3 /home/forge'],
                ], <<<YAML
                  workers:
                    -
                      queue: jobs
                      connection: rabbit
                      php: php73
                      daemon: true
                    -
                      queue: default
                      connection: redis
                      processes: 2
                      timeout: 0
                      sleep: 5
                      delay: 1
                    -
                      queue: default
                      connection: redis
                      tries: 3
                      environment: staging
                      force: true
                    -
                      queue: default
                      connection: redis
                      php: php73
                YAML,
            ],
            'additional local worker' => [
                <<<YAML
                workers:
                  -
                    queue: jobs
                    connection: rabbit
                    php: php73
                    daemon: true
                  -
                    queue: default
                    connection: redis
                    php: php73
                YAML, [
                    ['command' => 'php7.3 /home/forge'],
                ], <<<YAML
                  workers:
                    -
                      queue: default
                      connection: redis
                      php: php73
                YAML,
            ],
            'additional default local worker' => [
                <<<YAML
                workers:
                  -
                    queue: default
                    connection: redis
                  -
                    queue: default
                    connection: redis
                    php: php73
                YAML, [
                    ['command' => 'php7.2 /home/forge', 'daemon' => 1],
                ], <<<YAML
                  workers:
                    -
                      queue: default
                      connection: redis
                      php: php72
                YAML,
            ],
            'two identical additional default local workers' => [
                <<<YAML
                workers:
                  -
                    queue: default
                    connection: redis
                  -
                    queue: default
                    connection: redis
                    php: php73
                  -
                    queue: default
                    connection: redis
                YAML, [
                    ['command' => 'php7.3 /home/forge'],
                ], <<<YAML
                  workers:
                    -
                      queue: default
                      connection: redis
                      php: php73
                YAML,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider syncDownWorkersProvider
     */
    public function can_sync_forge_workers_down(string $before, array $workers, string $after)
    {
        $this->withConfig(<<<YAML
        production:
          id: 1
          server: 1
          {$before}
        YAML);

        $this->withForgeServer()
            ->withForgeSite()
            ->withForgePhpVersion(['used_on_cli' => true]);

        foreach ($workers as $attributes) {
            $this->withForgeWorker($attributes);
        }

        $this->inFixtureDir()->artisan('config:pull')
            ->expectsOutput('Updated the Forge configuration file.')
            ->assertExitCode(0);

        $this->assertInConfig($after);
    }

    /**
     * @test
     * @dataProvider syncDownWorkersProvider
     */
    public function can_sync_forge_workers_up(string $config, array $forge, array $create, array $delete)
    {
        $this->withConfig(<<<YAML
        production:
          id: 1
          server: 1
          {$config}
        YAML);

        $this->withForgeServer()
            ->withForgeSite()
            ->withForgePhpVersion(['used_on_cli' => true]);

        foreach ($forge as $attributes) {
            $this->withForgeWorker($attributes);
        }

        foreach ($create as $attributes) {
            $this->shouldCreateForgeWorker($attributes);
        }
        foreach ($delete as $attributes) {
            $this->shouldDeleteForgeWorker($attributes);
        }

        $this->inFixtureDir()->artisan('config:push')
            ->expectsOutput('Updated the Forge configuration file.')
            ->assertExitCode(0);
    }
}
