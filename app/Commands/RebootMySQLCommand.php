<?php

namespace App\Commands;

class RebootMySQLCommand extends RebootCommand
{
    protected $signature = 'reboot:mysql {environment=production} {--confirm}';

    protected $description = 'Reboot MySQL';

    protected $subject = 'MySQL';

    public function reboot()
    {
        $serverId = $this->configuration->get($this->argument('environment'), 'server');

        $this->forge->rebootMysql($serverId);
    }
}
