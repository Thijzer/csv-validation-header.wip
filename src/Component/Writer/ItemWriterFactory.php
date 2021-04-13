<?php

namespace Misery\Component\Writer;

use Misery\Component\Common\FileManager\LocalFileManager;
use Misery\Component\Common\Registry\RegisteredByNameInterface;

class ItemWriterFactory implements RegisteredByNameInterface
{
    public function createFromConfiguration(
        array $configuration,
        LocalFileManager $manager
    ) : ?ItemWriterInterface {
        if (isset($configuration['type'])) {
            if ($configuration['type'] === 'xml') {
                return new XmlWriter(
                    $manager->getWorkingDirectory() . DIRECTORY_SEPARATOR . $configuration['filename'],
                    $configuration['options'] ?? []
                );
            }
            if ($configuration['type'] === 'csv') {
                return new CsvWriter(
                    $manager->getWorkingDirectory() . DIRECTORY_SEPARATOR . $configuration['filename'],
                    $configuration['options'] ?? []
                );
            }
        }
    }

    public function getName(): string
    {
        return 'writer';
    }
}