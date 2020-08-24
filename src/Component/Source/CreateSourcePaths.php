<?php

namespace Misery\Component\Source;

class CreateSourcePaths
{
    public static function create(array $sources, string $path): array
    {
        $references = [];
        foreach ($sources as $source) {
            if (is_file($file = sprintf($path, $source))) {
                $references[$source] = $file;
            }
        }

        return $references;
    }
}