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

    public function syncUpWorkersProvider(): array
    {
        return [
            // 'omits default values' => [
            //     '', [
            //         ['daemon' => 0, 'processes' => 1],
            //     ], <<<YAML
            //       workers:
            //     YAML,
            // ],
            'local default worker' => [
                // Config
                <<<YAML
                workers:
                  -
                    queue: default
                    connection: redis
                YAML,
                // Forge
                [],
                // Create
                [
                    [
                        'queue' => 'default',
                        'connection' => 'redis',
                    ],
                ],
                // Delete
                [],
            ],
            'no local workers' => [
                // Config
                '',
                // Forge
                [
                    [
                        'id' => 30,
                        'queue' => 'default',
                        'connection' => 'redis',
                    ],
                ],
                // Create
                [],
                // Delete
                [
                    [
                        'id' => 30,
                        'queue' => 'default',
                    ],
                ],
            ],
            'one local default worker and two identical default Forge workers' => [
                // Config
                <<<YAML
                workers:
                  -
                    queue: default
                    connection: redis
                YAML,
                // Forge
                [
                    [
                        'id' => 1,
                        'queue' => 'default',
                        'connection' => 'redis',
                    ],
                    [
                        'id' => 2,
                        'queue' => 'default',
                        'connection' => 'redis',
                    ],
                ],
                // Create
                [],
                // Delete
                [
                    [
                        'id' => 2,
                        'queue' => 'default',
                    ],
                ],
            ],
            'local worker only slightly different from Forge worker' => [
                // Config
                <<<YAML
                workers:
                  -
                    queue: default
                    connection: redis
                    php: php74
                    daemon: true
                    timeout: 61
                YAML,
                // Forge
                [
                    [
                        'id' => 12,
                        'queue' => 'default',
                        'connection' => 'redis',
                        'daemon' => 1,
                        'timeout' => 60,
                        'command' => 'php7.4 /home',
                    ],
                ],
                // Create
                [
                    [
                        'queue' => 'default',
                        'connection' => 'redis',
                        'php' => 'php74',
                        'daemon' => true,
                        'timeout' => 61,
                        'php_version' => 'php74',
                    ],
                ],
                // Delete
                [
                    [
                        'id' => 12,
                        'queue' => 'default',
                    ],
                ],
            ],
        ];
    }

    /**
     * @test
     * @dataProvider syncUpWorkersProvider
     */
    public function can_sync_forge_workers_up(string $config, array $forge, array $create = [], array $delete = [])
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

        // COPIED FROM CONFIG DON'T KEEP THIS PUT IT SOMEWHERE CENTRAL
        $default = [
            'queue' => 'default',
            'connection' => 'redis',
            'php' => 'php80',
            'daemon' => false,
            'processes' => 1,
            'timeout' => 60,
            'sleep' => 10,
            'delay' => 0,
            'tries' => null,
            'environment' => null,
            'force' => false,
            'php_version' => 'php80',
        ];

        foreach ($create as $attributes) {
            $this->shouldCreateForgeWorker(array_merge($default, $attributes));
        }
        foreach ($delete as $attributes) {
            $this->shouldDeleteForgeWorker($attributes['id']);
        }

        $command = $this->inFixtureDir()->artisan('config:push --force');

        foreach ($create as $attributes) {
            $command->expectsOutput("Creating {$attributes['queue']} queue worker on {$attributes['connection']} connection...");
        }
        foreach ($delete as $attributes) {
            $command->expectsOutput("Deleting {$attributes['queue']} queue worker present on Forge but not listed locally...");
        }

        $command->assertExitCode(0);
    }

    /**
     * @test
     */
    public function can_skip_deleting_forge_workers_unless_force_option_passed()
    {
        $this->withConfig(<<<YAML
        production:
          id: 1
          server: 1
        YAML);

        $this->withForgeServer()
            ->withForgeSite()
            ->withForgePhpVersion(['used_on_cli' => true])
            ->withForgeWorker();

        $command = $this->inFixtureDir()->artisan('config:push');

        $command->expectsOutput('Found 1 queue workers present on Forge but not listed locally.');
        $command->expectsOutput('Run the command again with the `--force` option to delete them.');

        $command->assertExitCode(0);
    }
}
