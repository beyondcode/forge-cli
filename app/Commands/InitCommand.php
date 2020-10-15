<?php

namespace App\Commands;

use App\Commands\Concerns\EnsureHasToken;
use App\Support\Configuration;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Forge\Forge;
use Laravel\Forge\Resources\Server;
use Laravel\Forge\Resources\Site;
use LaravelZero\Framework\Commands\Command;

class InitCommand extends Command
{
    use EnsureHasToken;

    const PROJECT_TYPES = [
        'php' => 'General PHP/Laravel Application.',
        'html' => 'Static HTML site.',
        'symfony' => 'Symfony Application.',
        'symfony_dev' => 'Symfony (Dev) Application.',
        'symfony_four' => 'Symfony >4.0 Application.',
    ];

    /** @var Forge */
    protected $forge;

    /** @var string */
    protected $signature = 'init {environment=production}';

    /** @var string */
    protected $description = 'Initialize a new app ready to get deployed on Laravel Forge';

    /**
     * @param Forge $forge
     * @param Configuration $configuration
     */
    public function handle(Forge $forge, Configuration $configuration)
    {
        $this->ensureHasToken();

        $this->forge = $forge;

        $servers = $forge->servers();

        $selectedServer = $this->menu('Which server do you want to use?', collect($servers)->map(function (Server $server) {
            return "{$server->name} - [{$server->id}]";
        })->toArray())->open();

        exit_if(is_null($selectedServer));

        $server = $servers[$selectedServer];

        $linkSite = $this->confirm('Do you want to link this directory to an existing site?');

        if ($linkSite) {
            $sites = $forge->sites($server->id);

            $selectedSite = $this->menu('Which site do you want to link this project to?', collect($sites)->map(function (Site $site) {
                return "{$site->name} - [{$site->id}]";
            })->toArray())->open();

            exit_if(is_null($selectedSite));

            $site = $sites[$selectedSite];
        } else {
            $site = $this->createSite($server);
        }

        $configuration->initialize($this->argument('environment'), $server, $site, getcwd());

        $this->info('The project was successfully initialized.');
    }

    protected function createSite(Server $server)
    {
        $domain = $this->ask('What is the domain of your project?', basename(getcwd()));

        $selectedProjectType = $this->menu('What is your project type?', static::PROJECT_TYPES)->open();

        $directory = $this->ask('What is the public directory of your project?', '/public');

        exit_if(is_null($selectedProjectType));

        $this->info('Creating site on Forge');

        return $this->forge->createSite($server->id, [
            'domain' => $domain,
            'project_type' => $selectedProjectType,
            'directory' => $directory,
        ]);
    }
}
