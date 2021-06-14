<?php

namespace App\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laravel\Forge\Forge;
use Laravel\Forge\Resources\Daemon;
use Laravel\Forge\Resources\SecurityRule;
use Laravel\Forge\Resources\Server;
use Laravel\Forge\Resources\Site;
use Laravel\Forge\Resources\Webhook;
use Laravel\Forge\Resources\Worker;
use Symfony\Component\Yaml\Yaml;

class Configuration
{
    /** @var array */
    protected $config;

    /** @var Forge */
    protected $forge;

    public function __construct(Forge $forge)
    {
        $this->forge = $forge;

        try {
            $this->config = Yaml::parseFile(getcwd() . '/forge.yml');
        } catch (\Exception $e) {
            $this->config = [];
        }
    }

    public function initialize(string $environment, Server $server, Site $site, string $path)
    {
        $configFile = $path . '/forge.yml';

        $this->config[$environment] = $this->getConfigFormat($server, $site);

        $this->store($configFile);
    }

    public function store(string $configFile)
    {
        $flags = Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE | Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK;

        $configContent = Yaml::dump($this->config, 4, 2, $flags);

        file_put_contents($configFile, $configContent);
    }

    public function get(string $environment, string $key, $default = null)
    {
        return Arr::get($this->config, "{$environment}.{$key}", $default);
    }

    public function set(string $environment, string $key, $value)
    {
        Arr::set($this->config, "{$environment}.{$key}", $value);
    }

    protected function getConfigFormat(Server $server, Site $site)
    {
        $workers = $this->forge->workers($server->id, $site->id);

        return [
            'id' => $site->id,
            'name' => $site->name,
            'server' => $server->id,
            'quick-deploy' => $site->quickDeploy,
            'deployment' => $site->getDeploymentScript(),
            'webhooks' => $this->getWebhooks($server, $site),
            'daemons' => $this->getDaemons($server, $site),
            'workers' => $this->getWorkers($server, $site),
        ];
    }

    protected function getWebhooks(Server $server, Site $site)
    {
        return collect($this->forge->webhooks($server->id, $site->id))->map(function (Webhook $webhook) {
            return $webhook->url;
        })->values()->toArray();
    }

    protected function getDaemons(Server $server, Site $site)
    {
        return collect($this->forge->daemons($server->id))
            ->filter(function (Daemon $daemon) use ($site) {
                return Str::endsWith($daemon->command, " #{$site->id}");
            })
            ->map(function (Daemon $daemon) use ($site) {
                return [
                    'command' => Str::beforeLast($daemon->command, " #{$site->id}"),
                    'user' => $daemon->user,
                    'directory' => $daemon->directory,
                    'processes' => $daemon->processes,
                    'startsecs' => $daemon->startsecs,
                ];
        })->values()->toArray();
    }

    protected function getWorkers(Server $server, Site $site)
    {
        $cli = collect($this->forge->phpVersions($server->id))->firstWhere('usedOnCli', true)->version;

        $defaults = Defaults::worker($cli);

        return collect($this->forge->workers($server->id, $site->id))->map(function ($worker) use ($defaults) {
            $data = [
                'queue' => $worker->queue,
                'connection' => $worker->connection,
                'php_version' => str_replace('.', '', head(explode(' ', $worker->command))),
                'daemon' => (bool) $worker->daemon,
                'processes' => $worker->processes,
                'timeout' => $worker->timeout,
                'sleep' => $worker->sleep,
                'delay' => $worker->delay,
                'tries' => $worker->tries,
                'environment' => $worker->environment,
                'force' => (bool) $worker->force,
            ];

            $nonDefaults = collect($data)->filter(fn ($value, $key) => $value !== $defaults[$key])->keys()->toArray();

            return Arr::only($data, ['queue', 'connection', ...$nonDefaults]);
        })->toArray();
    }
}
