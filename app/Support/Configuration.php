<?php

namespace App\Support;

use Illuminate\Support\Arr;
use Laravel\Forge\Forge;
use Laravel\Forge\Resources\Server;
use Laravel\Forge\Resources\Site;
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

    public function initialize(Server $server, Site $site, string $path)
    {
        $configFile = $path . '/forge.yml';

        $configContent = Yaml::dump($this->getConfigFormat($server, $site), 4);

        file_put_contents($configFile, $configContent);
    }

    public function get($key, $default = null)
    {
        return Arr::get($this->config, $key, $default);
    }

    protected function getConfigFormat(Server $server, Site $site)
    {
        $workers = $this->forge->workers($server->id, $site->id);

        return [
            'id' => $site->id,
            'name' => $site->name,
            'server' => $server->id,
            'deployment' => explode("\n", $site->getDeploymentScript()),
            'workers' => collect($workers)->map(function (Worker $worker) {
                return $worker->attributes;
            })->values()->toArray(),
        ];
    }
}
