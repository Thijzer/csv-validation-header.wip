<?php

namespace Misery\Component\Writer;

use Assert\Assert;
use Misery\Component\Common\Collection\ArrayCollection;
use Misery\Component\Common\Registry\RegisteredByNameInterface;
use Misery\Component\Reader\ItemCollection;
use Symfony\Component\Yaml\Yaml;

class ItemWriterFactory implements RegisteredByNameInterface
{
    public function createFromConfiguration(
        array $configuration,
        string $workingDirectory
    ) : ItemWriterInterface {
        Assert::that(
            $configuration['type'],
            'type must be filled in.'
        )->notEmpty()->string()->inArray(['xml', 'csv', 'yaml', 'yml', 'xlsx']);

        $filename = $workingDirectory . DIRECTORY_SEPARATOR . $configuration['filename'];
        if ($configuration['type'] === 'xml') {
            return new XmlWriter(
                $filename,
                $configuration['options'] ?? []
            );
        }
        if ($configuration['type'] === 'csv') {
            $configuration['filename'] = $filename;
            return CsvWriter::createFromArray($configuration);
        }
        if ($configuration['type'] === 'yml' || $configuration['type'] === 'yaml') {
            $configuration['filename'] = $filename;
            return new YamlWriter($configuration['filename']);
        }

        if ($configuration['type'] === 'xlsx') {
            $configuration['filename'] = $filename;
            return new XlsxWriter($configuration);
        }

        throw new \RuntimeException('Impossible Exception');
    }

    public function getName(): string
    {
        return 'writer';
    }
}