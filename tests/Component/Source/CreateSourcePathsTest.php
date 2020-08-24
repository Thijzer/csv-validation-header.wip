<?php

namespace Tests\Misery\Component\Source;

use Misery\Component\Source\CreateSourcePaths;
use PHPUnit\Framework\TestCase;

class CreateSourcePathsTest extends TestCase
{
    public function test_init_create_source_paths(): void
    {
        $path = __DIR__ . '/../../examples/%s.csv';

        $sourcePaths = createSourcePaths::create(['users'], $path);

        $this->assertSame($sourcePaths, [
            'users' => sprintf($path, 'users'),
        ]);
    }
}