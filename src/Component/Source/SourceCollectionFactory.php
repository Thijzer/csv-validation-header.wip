<?php

namespace Misery\Component\Source;

use Misery\Component\Common\Cursor\CachedCursor;
use Misery\Component\Common\Cursor\FunctionalCursor;
use Misery\Component\Common\FileManager\LocalFileManager;
use Misery\Component\Common\Registry\RegisteredByNameInterface;
use Misery\Component\Encoder\ItemEncoder;
use Misery\Component\Encoder\ItemEncoderFactory;
use Misery\Component\Parser\CsvParser;
use Misery\Component\Parser\XmlParser;
use Misery\Component\Reader\ItemReader;
use Misery\Component\Reader\ItemReaderInterface;

class SourceCollectionFactory implements RegisteredByNameInterface
{
    public function createFromFileManager(LocalFileManager $manager, SourceCollection $sourceCollection = null): SourceCollection
    {
        $sourceCollection = $sourceCollection ?: new SourceCollection('manager');
        foreach ($manager->listFiles() as $file) {
            $path = pathinfo($file);
            if ($path['extension'] === 'csv') {
                $sourceCollection->add(
                    new Source(new ItemReader(CsvParser::create($file)), $path['basename'])
                );
            }
            if ($path['extension'] === 'xml') {
                $sourceCollection->add(
                    new Source(new ItemReader(XmlParser::create($file)), $path['basename'])
                );
            }
        }

        return $sourceCollection;
    }

    public function createFromConfiguration(array $configuration, SourceCollection $sourceCollection = null): SourceCollection
    {
        // update our sources with more intel

//        $sourcePaths = CreateSourcePaths::create(
//            $configuration['sources']['list'],
//            $manager->getWorkingDirectory() . '/%s.csv',
//            $configuration[] . DIRECTORY_SEPARATOR . $configuration['sources']['type'] . '/%s.yaml'
//        );
    }
    public static function create(ItemEncoderFactory $encoderFactory, array $sourcePaths): SourceCollection
    {
        // TODO open up the SourceCollection
        $sources = new SourceCollection('akeneo/csv');

        foreach ($sourcePaths as $reference => $sourcePath) {
            $configuration = $sourcePath['blueprint'] ?? [];
            $sources->add(new Source(
                self::createEncodedReader(
                    $configuration,
                    $encoderFactory->createItemEncoder($configuration),
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

    public function getName(): string
    {
        return 'source';
    }
}
