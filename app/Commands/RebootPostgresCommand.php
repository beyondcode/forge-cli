<?php

namespace App\Commands;

class RebootPostgresCommand extends RebootCommand
{
    protected $signature = 'reboot:postgres {environment=production} {--confirm}';

    protected $description = 'Reboot Postgres';

    protected $subject = 'Postgres';

    public function reboot()
    {
        $serverId = $this->configuration->get($this->argument('environment'), 'server');

        $this->forge->rebootPostgres($serverId);
    }
}
