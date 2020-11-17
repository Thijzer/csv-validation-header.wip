<?php

namespace Tests\Misery\Component\Source;

use Misery\Component\Source\Source;
use Misery\Component\Source\SourceCollection;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

class SourceCollectionTest extends TestCase
{
    public function test_init_source_collection(): void
    {
        /** @var Source|ObjectProphecy $source */
        $source = $this->prophesize(Source::class);
        $source->getAlias()->willReturn('products');

        $sourceCollection = new SourceCollection('akeneo/csv');
        $sourceCollection->add($objectSource = $source->reveal());

        $this->assertSame($objectSource, $sourceCollection->get('products'));
    }
}