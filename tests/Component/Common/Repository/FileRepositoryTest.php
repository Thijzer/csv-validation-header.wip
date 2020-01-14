<?php

namespace Tests\Misery\Component\Common\Repository;

use Misery\Component\Csv\Reader\CsvParser;
use Misery\Component\Csv\Reader\ItemCollection;
use Misery\Component\Csv\Reader\RowReader;
use PHPUnit\Framework\TestCase;

class FileRepositoryTest extends TestCase
{
    private $items = [
        [
            'first_name' => 'Gordie',
        ],
        [
            'first_name' => 'Frans',
        ],
    ];

    // TODO we should implement te RowReader behaviour to a matching Repository Interface

    public function test_find(): void
    {
        $reader = new RowReader($items = new ItemCollection($this->items));

        $data = $reader->find(['first_name' => 'Gordie']);

        $this->assertSame([$items->getItems()[0]], $data->getItems());
    }
}