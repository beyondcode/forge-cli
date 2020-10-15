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

        $config = $this->config;

        $config[$environment] = $this->getConfigFormat($server, $site);

        $configContent = Yaml::dump($config, 4, 2, Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE);

        file_put_contents($configFile, $configContent);
    }

    public function get(string $environment, string $key, $default = null)
    {
        return Arr::get($this->config, "{$environment}.{$key}", $default);
    }

    protected function getConfigFormat(Server $server, Site $site)
    {
        $workers = $this->forge->workers($server->id, $site->id);

        return [
            'id' => $site->id,
            'name' => $site->name,
            'server' => $server->id,
            'quick-deploy' => $site->quickDeploy,
            'deployment' => explode("\n", $site->getDeploymentScript()),
            'webhooks' => $this->getWebhooks($server, $site),
            'daemons' => $this->getDaemons($server, $site),
            'workers' => collect($workers)->map(function (Worker $worker) {
                return $worker->attributes;
            })->values()->toArray(),
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
}
