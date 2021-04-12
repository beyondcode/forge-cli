<?php

namespace App\Commands;

use App\Support\Configuration;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Forge\Forge;

class CreateSshKeyCommand extends ForgeCommand
{
    protected $signature = 'ssh {environment=production}';

    protected $description = 'Create a new ssh key on Laravel Forge';

    public function handle(Forge $forge, Configuration $configuration)
    {
        if (! $this->ensureHasToken()) {
            return 1;
        }
        if (! $this->ensureHasForgeConfiguration()) {
            return 1;
        }

        $environment = $this->argument('environment');

        $serverId = $configuration->get($environment, 'server');

        $name = $this->ask('What is the key name', 'macbook');
        $path = $this->ask('Where is the public key file', ($_SERVER['HOME'] ?? $_SERVER['USERPROFILE'] ?? __DIR__).'/.ssh/id_rsa.pub');
        $username = $this->ask('What is the user name', 'forge');

        $key = file_get_contents($path);

        $forge->createSSHKey($serverId, compact('name', 'key', 'username'));

        $this->info('The key has been created');
    }
}
