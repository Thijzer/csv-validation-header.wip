<?php

namespace Misery\Component\Source;

use Misery\Component\Common\Cursor\CachedCursor;
use Misery\Component\Common\Cursor\FunctionalCursor;
use Misery\Component\Encoder\ItemEncoder;
use Misery\Component\Encoder\ItemEncoderFactory;
use Misery\Component\Parser\CsvParser;
use Misery\Component\Reader\ItemReader;
use Misery\Component\Reader\ItemReaderInterface;

class SourceCollectionFactory
{
    public static function create(ItemEncoderFactory $encoderFactory, array $sourcePaths): SourceCollection
    {
        // TODO open up the SourceCollection
        $sources = new SourceCollection('akeneo/csv');

        foreach ($sourcePaths as $reference => $sourcePath) {
            $configuration = $sourcePath['blueprint'];
            $sources->add(new Source(
                self::createEncodedReader(
                    $configuration,
                    $encoderFactory->createItemEncoder($sources, $configuration),
                    $sourcePath['source']
                ),
                $reference
            ));
        }

        return $sources;
    }

    private static function createEncodedReader(array $configuration, ItemEncoder $encoder, string $source): ItemReaderInterface
    {
        if ($configuration['parse']['type'] === 'csv') {

            $format = $configuration['parse']['format'];

            return new ItemReader(
                new CachedCursor(
                    new FunctionalCursor(
                        CsvParser::create(
                            $source,
                            $format['delimiter'],
                            $format['enclosure']
                        ), function ($item) use ($encoder) {
                        return $encoder->encode($item);
                    }
                    ),
                    [
                        'cache_size' => CachedCursor::LARGE_CACHE_SIZE,
                    ]
                )
            );
        }
    }
}
