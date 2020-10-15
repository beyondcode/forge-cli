<?php

namespace App\Commands;

class RebootNginxCommand extends RebootCommand
{
    protected $signature = 'reboot:nginx {environment=production} {--confirm}';

    protected $description = 'Reboot Nginx';

    protected $subject = 'Nginx';

    public function reboot()
    {
        $serverId = $this->configuration->get($this->argument('environment'), 'server');

        $this->forge->rebootNginx($serverId);
    }
}
