<?php

namespace Tests\Misery\Component\Source;

use Misery\Component\Decoder\ItemDecoder;
use Misery\Component\Encoder\ItemEncoder;
use Misery\Component\Parser\CsvParser;
use Misery\Component\Reader\ItemReaderInterface;
use Misery\Component\Source\Source;
use PHPUnit\Framework\TestCase;

class SourceTest extends TestCase
{
    public function test_init_source(): void
    {
        $file = __DIR__ . '/../../examples/users.csv';

        $source = new Source(
            new ItemEncoder([]),
            new ItemDecoder([]),
            [
                'parse' =>
                    [
                        'type' => 'csv',
                        'format' => [
                            'delimiter' => ';',
                            'enclosure' => '"',
                        ]
                    ]
            ],
            $file,
            'products'
        );

        $this->assertInstanceOf(ItemReaderInterface::class, $source->getReader());

        $this->assertSame($source->getReader()->read(), CsvParser::create($file)->current());
    }
}