<?php

namespace App\Commands;

class RebootMySQLCommand extends RebootCommand
{
    protected $signature = 'reboot:mysql {--confirm}';

    protected $description = 'Reboot MySQL';

    protected $subject = 'MySQL';

    public function reboot()
    {
        $serverId = $this->configuration->get('server');

        $this->forge->rebootMysql($serverId);
    }
}
