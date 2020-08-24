<?php

namespace Misery\Component\Source;

use Misery\Component\Decoder\ItemDecoderFactory;
use Misery\Component\Encoder\ItemEncoderFactory;
use Symfony\Component\Yaml\Yaml;

class SourceCollectionFactory
{
    public static function create(ItemEncoderFactory $encoderFactory, ItemDecoderFactory $decoderFactory, array $references, string $bluePrintDir): SourceCollection
    {
        // TODO open up the SourceCollection
        $sources = new SourceCollection('akeneo/csv');

        foreach ($references as $reference => $file) {
            if (is_file($file)) {
                $configuration = self::createConfigurationFromBluePrint($bluePrintDir, $reference);
                $sources->add(new Source(
                    SourceType::file(),
                    $encoderFactory->createItemEncoder($configuration),
                    $decoderFactory->createItemDecoder($configuration),
                    $file,
                    $reference
                ));
            }
        }

        return $sources;
    }

    private static function createConfigurationFromBluePrint(string $bluePrintDir, string $reference): array
    {
        $configurationFile = $bluePrintDir . DIRECTORY_SEPARATOR . $reference . '.yaml';

        if (is_file($configurationFile)) {
            return Yaml::parseFile($configurationFile);
        }

        return [];
    }
}