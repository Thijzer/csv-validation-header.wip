<?php

namespace Misery\Component\Reader;

use Assert\Assert;
use Misery\Component\Common\FileManager\LocalFileManager;
use Misery\Component\Common\Registry\RegisteredByNameInterface;
use Misery\Component\Parser\CsvParser;
use Misery\Component\Parser\XmlParser;

class ItemReaderFactory implements RegisteredByNameInterface
{
    public function createFromConfiguration(
        array $configuration,
        LocalFileManager $manager
    ) : ItemReaderInterface {
        Assert::that(
            $configuration['type'],
            'type must be filled in.'
        )->notEmpty()->string()->inArray(['xml', 'csv']);

        if ($configuration['type'] === 'xml') {
            return new ItemReader(XmlParser::create(
                $manager->getWorkingDirectory(). DIRECTORY_SEPARATOR . $configuration['filename'],
                $configuration['container'] ?? null
            ));
        }
        if ($configuration['type'] === 'csv') {
            return new ItemReader(CsvParser::create(
                $manager->getWorkingDirectory(). DIRECTORY_SEPARATOR . $configuration['filename'],
                $configuration['delimiter'] ?? CsvParser::DELIMITER,
                $configuration['enclosure'] ?? CsvParser::ENCLOSURE
            ));
        }

        throw new \RuntimeException('Impossible Exception');
    }

    public function getName(): string
    {
        return 'reader';
    }
}