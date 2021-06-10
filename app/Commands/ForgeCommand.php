<?php

namespace App\Commands;

use App\Commands\Concerns\EnsureHasToken;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class ForgeCommand extends Command
{
    use EnsureHasToken;

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        if (! $this->ensureHasToken()) {
            return static::FAILURE;
        }

        if (! file_exists(getcwd() . '/forge.yml')) {
            $this->error('You have not yet linked this project to Forge. Run `forge init` first.');

            return static::FAILURE;
        }

        return parent::execute($input, $output);
    }
}
