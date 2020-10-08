<?php

namespace App\Commands;

class RebootNginxCommand extends RebootCommand
{
    protected $signature = 'reboot:nginx {--confirm}';

    protected $description = 'Reboot Nginx';

    protected $subject = 'Nginx';

    public function reboot()
    {
        $serverId = $this->configuration->get('server');

        $this->forge->rebootNginx($serverId);
    }
}
