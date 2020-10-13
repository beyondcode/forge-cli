<?php

namespace App\Commands;

class RebootPostgresCommand extends RebootCommand
{
    protected $signature = 'reboot:postgres {--confirm}';

    protected $description = 'Reboot Postgres';

    protected $subject = 'Postgres';

    public function reboot()
    {
        $serverId = $this->configuration->get('server');

        $this->forge->rebootPostgres($serverId);
    }
}
