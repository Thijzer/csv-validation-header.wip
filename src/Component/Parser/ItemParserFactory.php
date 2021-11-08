<?php

namespace Misery\Component\Parser;

use Assert\Assert;
use Misery\Component\Common\Collection\ArrayCollection;
use Misery\Component\Common\Cursor\CursorInterface;
use Misery\Component\Common\FileManager\LocalFileManager;
use Misery\Component\Common\Registry\RegisteredByNameInterface;
use Misery\Component\Reader\ItemCollection;
use Misery\Component\Writer\CsvWriter;

class ItemParserFactory implements RegisteredByNameInterface
{
    public function createFromConfiguration(
        array $configuration,
        LocalFileManager $manager
    ) : CursorInterface {
        $type = strtolower($configuration['type']);
        Assert::that(
            $type,
            'type must be filled in.'
        )->notEmpty()->string()->inArray(['xml', 'csv', 'xlsx', 'list']);

        if ($type === 'xml') {
            return XmlParser::create(
                $manager->getWorkingDirectory(). DIRECTORY_SEPARATOR . $configuration['filename'],
                $configuration['container'] ?? null
            );
        }
        if ($type === 'csv') {
            return CsvParser::create(
                $manager->getWorkingDirectory(). DIRECTORY_SEPARATOR . $configuration['filename'],
                $configuration['delimiter'] ?? CsvParser::DELIMITER,
                $configuration['enclosure'] ?? CsvParser::ENCLOSURE,
                $configuration['escape'] ?? CsvParser::ESCAPE,
                $configuration['invalid_lines'] ?? CsvParser::INVALID_STOP
            );
        }
        if ($type === 'xlsx') {
            return XlsxParser::create(
                $manager->getWorkingDirectory(). DIRECTORY_SEPARATOR . $configuration['filename']
            );
        }
        if ($configuration['type'] === 'list') {
            return new ItemCollection();
        }

        throw new \RuntimeException('Impossible Exception');
    }

    public function getName(): string
    {
        return 'parser';
    }
}