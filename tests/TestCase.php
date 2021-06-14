<?php

namespace Tests;

use LaravelZero\Framework\Testing\TestCase as BaseTestCase;
use Mockery;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use MocksForge;

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    protected function withConfig(string $yaml): void
    {
        file_put_contents(implode(DIRECTORY_SEPARATOR, [__DIR__, 'fixtures', 'forge.yml']), $yaml);
    }

    protected function inFixtureDir(): static
    {
        chdir(__DIR__ . DIRECTORY_SEPARATOR . 'fixtures');

        return $this;
    }

    protected function assertConfig(string $yaml): void
    {
        $this->assertSame($yaml . PHP_EOL, file_get_contents(implode(DIRECTORY_SEPARATOR, [__DIR__, 'fixtures', 'forge.yml'])));
    }

    protected function assertInConfig(string $yaml): void
    {
        $this->assertStringContainsString($yaml, file_get_contents(implode(DIRECTORY_SEPARATOR, [__DIR__, 'fixtures', 'forge.yml'])));
    }
}
