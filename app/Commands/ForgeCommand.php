<?php

namespace App\Commands;

use App\Commands\Concerns\EnsureHasForgeConfiguration;
use App\Commands\Concerns\EnsureHasToken;
use LaravelZero\Framework\Commands\Command;

abstract class ForgeCommand extends Command
{
    use EnsureHasToken;
    use EnsureHasForgeConfiguration;
}
