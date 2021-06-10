<?php

namespace App\Commands;

use App\Support\Configuration;
use Laravel\Forge\Forge;

abstract class RebootCommand extends ForgeCommand
{
    /** @var Forge */
    protected $forge;

    /** @var Configuration */
    protected $configuration;

    /** @var string the name of what is being rebooted */
    protected $subject = '';

    public function handle(Forge $forge, Configuration $configuration)
    {
        $this->forge = $forge;
        $this->configuration = $configuration;

        if (! $this->option('confirm')) {
            $this->warn('Rebooting ' . $this->subject . ' requires confirmation');
            $this->warn('Please use --confirm to confirm that you want to reboot ' . $this->subject);

            return 1;
        }

        $this->info('Rebooting ' . $this->subject);

        $this->reboot();

        $this->info('Depending on the server, this may take some time and may cause temporary downtime');
    }

    abstract public function reboot();
}
