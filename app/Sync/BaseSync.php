<?php

namespace App\Sync;

use App\Support\Configuration;
use Illuminate\Console\OutputStyle;
use Laravel\Forge\Forge;
use Laravel\Forge\Resources\Server;
use Laravel\Forge\Resources\Site;

abstract class BaseSync
{
    /** @var Forge */
    protected $forge;

    /** @var Configuration */
    protected $config;

    public function __construct(Forge $forge, Configuration $config)
    {
        $this->forge = $forge;
        $this->config = $config;
    }

    abstract public function sync(Server $server, Site $site, OutputStyle $output, bool $force = false): void;
}
