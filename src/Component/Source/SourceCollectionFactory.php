<?php

namespace Misery\Component\Source;

use Misery\Component\Decoder\ItemDecoderFactory;
use Misery\Component\Encoder\ItemEncoderFactory;

class SourceCollectionFactory
{
    public static function create(ItemEncoderFactory $encoderFactory, ItemDecoderFactory $decoderFactory, array $sourcePaths): SourceCollection
    {
        // TODO open up the SourceCollection
        $sources = new SourceCollection('akeneo/csv');

        foreach ($sourcePaths as $reference => $sourcePath) {
            $configuration = $sourcePath['blueprint'];
            $sources->add(new Source(
                $encoderFactory->createItemEncoder($configuration),
                $decoderFactory->createItemDecoder($configuration),
                $configuration,
                $sourcePath['source'],
                $reference
            ));
        }

        return $sources;
    }
}