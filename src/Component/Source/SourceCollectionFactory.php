<?php

namespace Misery\Component\Source;

use Misery\Component\Decoder\ItemDecoderFactory;
use Misery\Component\Encoder\ItemEncoderFactory;

class SourceCollectionFactory
{
    public static function createFromApi(ItemEncoderFactory $encoderFactory, ItemDecoderFactory $decoderFactory, array $sourceConf): SourceCollection
    {
        // todo base decode decompress and store on a save tmp location
        unset($sourceConf['sources']['transport']['data']);

        $p = new \PharData('/path/to/my.tar.gz');
        $p->decompress(); // creates /path/to/my.tar
        // unarchive from the tar
        $phar = new \PharData('/path/to/my.tar');
        $phar->extractTo('/full/path');

        // todo make a valid sourcePaths from the api data

        $sourcePaths = CreateSourcePaths::create(
            $sourceConf['sources']['list'],
            $sourceConf['sources']['type']
        );

        /**
         * filename =>
         * 'blueprint' => []
         * 'path' => filePath
         */


        // TODO open up the SourceCollection
        $sources = new SourceCollection($sourceConf['sources']['type']);

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