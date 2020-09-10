<?php

namespace Misery\Component\Source;

use Symfony\Component\Yaml\Yaml;

class CreateSourcePaths
{
    public static function create(array $sources, string $sourcePath, string $bluePrintPath = null): array
    {
        $references = [];
        foreach ($sources as $source) {
            if (is_file($file = sprintf($sourcePath, $source))) {
                $references[$source]['source'] = $file;
            }
            if (is_file($file = sprintf($bluePrintPath, $source))) {
                $references[$source]['blueprint'] = Yaml::parseFile($file);
            }
        }

        return $references;
    }
}