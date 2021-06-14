<?php

namespace App\Sync;

use App\Support\Defaults;
use Illuminate\Console\OutputStyle;
use Laravel\Forge\Resources\Server;
use Laravel\Forge\Resources\Site;
use Laravel\Forge\Resources\Worker;

class WorkerSync extends BaseSync
{
    public function sync(string $environment, Server $server, Site $site, OutputStyle $output, bool $force = false): void
    {
        $workers = collect($this->config->get($environment, 'workers', []));
        $forgeWorkers = collect($this->forge->workers($server->id, $site->id))->keyBy('id');

        // Create workers that are defined locally but do not exist on Forge
        $workers->reject(function (array $worker) use (&$forgeWorkers, $server, $site) {
            if ($match = $forgeWorkers->first(fn (Worker $forge) => $this->equivalent($server, $forge, $worker))) {
                // Remove each found worker from the list of 'unmatched' workers on Forge
                $forgeWorkers->forget($match->id);

                return true;
            }
        })->map(function (array $worker) use ($server, $site, $output) {
            $data = $this->getWorkerPayload($server, $worker);

            $output->writeln("Creating {$data['queue']} queue worker on {$data['connection']} connection...");

            $this->forge->createWorker($server->id, $site->id, $data);
        });

        if ($forgeWorkers->isNotEmpty()) {
            if ($force) {
                $forgeWorkers->map(function (Worker $worker) use ($server, $site, $output) {
                    $output->writeln("Deleting {$worker->queue} queue worker present on Forge but not listed locally...");

                    $this->forge->deleteWorker($server->id, $site->id, $worker->id);
                });
            } else {
                $output->writeln("Found {$forgeWorkers->count()} queue workers present on Forge but not listed locally.");
                $output->writeln('Run the command again with the `--force` option to delete them.');
            }
        }
    }

    protected function equivalent(Server $server, Worker $worker, array $config): bool
    {
        $cli = collect($this->forge->phpVersions($server->id))->firstWhere('usedOnCli', true)->version;

        $defaults = Defaults::worker($cli);

        $forgeWorker = [
            'queue' => $worker->queue,
            'connection' => $worker->connection,
            'timeout' => $worker->timeout,
            'delay' => $worker->delay,
            'sleep' => $worker->sleep,
            'tries' => $worker->tries,
            'environment' => $worker->environment,
            'daemon' => (bool) $worker->daemon,
            'force' => (bool) $worker->force,
            'php_version' => str_replace('.', '', head(explode(' ', $worker->command))),
            'processes' => $worker->processes,
        ];

        foreach (array_merge($defaults, $config) as $key => $value) {
            if ($forgeWorker[$key] !== $value) {
                return false;
            }
        }

        return true;
    }

    protected function getWorkerPayload(Server $server, array $worker): array
    {
        $cli = collect($this->forge->phpVersions($server->id))->firstWhere('usedOnCli', true)->version;

        return array_merge(Defaults::worker($cli), $worker);
    }
}
