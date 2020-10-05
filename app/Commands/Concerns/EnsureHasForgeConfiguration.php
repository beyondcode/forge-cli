<?php

namespace App\Commands\Concerns;

trait EnsureHasForgeConfiguration
{
    public function ensureHasForgeConfiguration()
    {
        if (!file_exists(getcwd() . '/forge.yml')) {
            $this->error('You have not yet linked this project to Forge.');
            return false;
        }

        return true;
    }
}
