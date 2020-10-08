<?php

namespace App\Commands;

class RebootServerCommand extends RebootCommand
{
    protected $signature = 'reboot:server {--confirm}';

    protected $description = 'Reboot the server';

    protected $subject = 'the server';

    public function reboot()
    {
        $serverId = $this->configuration->get('server');

        $this->forge->rebootServer($serverId);
    }
}
