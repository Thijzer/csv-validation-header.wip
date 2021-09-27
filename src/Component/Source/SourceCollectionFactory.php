<?php

namespace Misery\Component\Source;

use Assert\Assert;
use Misery\Component\Common\Cursor\CachedCursor;
use Misery\Component\Common\FileManager\LocalFileManager;
use Misery\Component\Common\Registry\RegisteredByNameInterface;
use Misery\Component\Encoder\ItemEncoderFactory;
use Misery\Component\Item\Processor\EncoderProcessor;
use Misery\Component\Item\Processor\NullProcessor;
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
                    Source::createSimple(CsvParser::create($file), $path['basename'])
                );
            }
            if ($path['extension'] === 'xml') {
                $sourceCollection->add(
                    Source::createSimple(XmlParser::create($file), $path['basename'])
                );
            }
        }

        return $sourceCollection;
    }

    public function createFromConfiguration(array $configuration, SourceCollection $sourceCollection = null): SourceCollection
    {
        foreach ($configuration['sources'] as $sourceName) {
        }
        return $sourceCollection;
    }

    public static function create(ItemEncoderFactory $encoderFactory, array $sourcePaths): SourceCollection
    {
        // TODO open up the SourceCollection
        $sources = new SourceCollection('akeneo/csv');

        foreach ($sourcePaths as $reference => $sourcePath) {
            $configuration = $sourcePath['blueprint'] ?? [];
            $sources->add(new Source(
                self::createEncodedCursor(
                    $configuration,
                    $sourcePath['source']
                ),
                new EncoderProcessor($encoderFactory->createItemEncoder($configuration)),
                new NullProcessor(),
                $reference
            ));
        }

        return $sources;
    }

    private static function createEncodedCursor(array $configuration, string $source): CachedCursor
    {
        Assert::that($configuration['parse'])->keyIsset('type')->notEmpty()->inArray(['csv']);

        $format = $configuration['parse']['format'];

        return new CachedCursor(
            CsvParser::create(
                $source,
                $format['delimiter'],
                $format['enclosure']
            ),
            ['cache_size' => CachedCursor::LARGE_CACHE_SIZE]
        );
    }

    public function getName(): string
    {
        return 'source';
    }
}
