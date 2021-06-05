<?php

namespace Tests;

use Laravel\Forge\Forge;
use LaravelZero\Framework\Testing\TestCase as BaseTestCase;
use Mockery;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use FakesForgeResources;

    protected $forge;

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    protected function withConfig(string $yaml): void
    {
        file_put_contents(implode(DIRECTORY_SEPARATOR, [__DIR__, 'fixtures', 'forge.yml']), $yaml);
    }

    protected function assertConfig(string $yaml): void
    {
        $this->assertSame($yaml, file_get_contents(implode(DIRECTORY_SEPARATOR, [__DIR__, 'fixtures', 'forge.yml'])));
    }

    protected function mockForge(): void
    {
        $this->forge = $this->mock(Forge::class);
    }
}
