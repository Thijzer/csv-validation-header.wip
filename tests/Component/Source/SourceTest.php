<?php

namespace Tests\Misery\Component\Source;

use Misery\Component\Decoder\ItemDecoder;
use Misery\Component\Encoder\ItemEncoder;
use Misery\Component\Item\Processor\NullProcessor;
use Misery\Component\Parser\CsvParser;
use Misery\Component\Reader\ItemReader;
use Misery\Component\Reader\ItemReaderInterface;
use Misery\Component\Source\Source;
use PHPUnit\Framework\TestCase;

class SourceTest extends TestCase
{
    public function test_init_source(): void
    {
        $file = __DIR__ . '/../../examples/users.csv';

        $parser = CsvParser::create($file);
        $current = $parser->current();

        $source = new Source(
            new ItemReader(clone $parser),
            new NullProcessor(),
            new NullProcessor(),
            'products'
        );

        $this->assertInstanceOf(ItemReaderInterface::class, $source->getReader());

        $this->assertSame($source->getReader()->read(), $current);
    }
}