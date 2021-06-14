<?php

namespace App\Sync;

use Closure;
use Illuminate\Support\Str;
use Laravel\Forge\Resources\Daemon;
use Laravel\Forge\Resources\Server;
use Laravel\Forge\Resources\Site;
use Laravel\Forge\Resources\Webhook;

class DaemonSync extends BaseSync
{

    public function sync(string $environment, Server $server, Site $site, Closure $output, bool $force = false): void
    {
        $daemons = collect($this->config->get($environment, 'daemons', []));
        $daemonsOnForge = collect($this->forge->daemons($server->id))->filter(function (Daemon $daemon) use ($site) {
            return Str::endsWith($daemon->command, " #{$site->id}");
        });

        // Delete Daemons on Forge but removed/modified locally
        $deleteDaemons = collect($daemonsOnForge)
            ->reject(function (Daemon $daemon) use ($daemons, $site) {
                return $daemons->contains(function ($daemonFromConfig) use ($daemon, $site) {
                    return
                        $daemonFromConfig['command'] === Str::beforeLast($daemon->command, " #{$site->id}") &&
                        $daemonFromConfig['user'] === $daemon->user &&
                        $daemonFromConfig['directory'] === $daemon->directory &&
                        $daemonFromConfig['processes'] === $daemon->processes &&
                        $daemonFromConfig['startsecs'] === $daemon->startsecs;
                });
            });

        $deleteDaemons->map(function (Daemon $daemon) use ($server, $site, $output) {
            $command = Str::beforeLast($daemon->command, " #{$site->id}");

            $output("Deleting daemon: {$command}");
            $daemon->delete();
        });

        // Create daemons not on Forge
        $daemons->diffUsing($daemonsOnForge->map(function (Daemon $daemon) use ($site) {
            return [
                'command' => Str::beforeLast($daemon->command, " #{$site->id}"),
                'user' => $daemon->user,
                'directory' => $daemon->directory,
                'processes' => $daemon->processes,
                'startsecs' => $daemon->startsecs,
            ];
        }), function ($a, $b){
            return count(array_diff($a, $b)) > 0;
        })->map(function ($daemonData) use ($server, $site, $output) {
            $output("Creating daemon: {$daemonData['command']}");
            $daemonData['command'] .= " #{$site->id}";
            $this->forge->createDaemon($server->id, $daemonData);
        });
    }
}
