<?php

namespace Tests\Feature;

use Laravel\Forge\Forge;
use Tests\TestCase;

class InfoCommandTest extends TestCase
{
    /** @test */
    public function it_throws_an_error_when_running_in_an_unlinked_directory()
    {
        chdir(__DIR__.'/../fixtures');

        $this->artisan('info')
            ->expectsOutput('You have not yet linked this project to Forge.')
            ->assertExitCode(1);
    }
}
