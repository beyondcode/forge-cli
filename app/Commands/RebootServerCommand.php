<?php

namespace App\Commands;

class RebootServerCommand extends RebootCommand
{
    protected $signature = 'reboot:server {environment=production} {--confirm}';

    protected $description = 'Reboot the server';

    protected $subject = 'the server';

    public function reboot()
    {
        $serverId = $this->configuration->get($this->argument('environment'), 'server');

        $this->forge->rebootServer($serverId);
    }
}
