<?php

namespace Misery\Component\Encoder;

use Misery\Component\Common\Format\ArrayFormat;
use Misery\Component\Common\Format\StringFormat;
use Misery\Component\Common\Modifier\CellModifier;
use Misery\Component\Common\Modifier\RowModifier;
use Misery\Component\Common\Options\OptionsInterface;

class ItemEncoder
{
    private $configurationRules;

    public function __construct(array $configurationRules)
    {
        $this->configurationRules = $configurationRules;
    }

    public function encode(array $item): array
    {
        foreach ($this->configurationRules['item'] ?? [] as $property => $matches) {
            foreach ($matches as $match) {
                $this->processMatch($item, $property, $match);
            }
        }

        foreach ($this->configurationRules['property'] ?? [] as $property => $matches) {
            if (isset($item[$property])) {
                foreach ($matches as $match) {
                    $this->processMatch($item, $property, $match);
                }
            }
        }

        return $item;
    }

    private function processMatch(array &$item, string $property, array $match): void
    {
        $class = $match['class'];

        if ($class instanceof OptionsInterface && !empty($match['options'])) {
            $class->setOptions($match['options']);
        }

//        if ($class instanceof ItemReaderAwareInterface) {
//            $class->setReader($this->readers['current']);
//            return;
//        }

        switch (true) {
            case $class instanceof ArrayFormat:
                $item = $class->format($item);
                break;
            case $class instanceof CellModifier:
                $item[$property] = $class->modify($item[$property]);
                break;
            case $class instanceof StringFormat:
                $item[$property] = $class->format($item[$property]);
                break;
            case $class instanceof RowModifier:
                $item = $class->modify($item);
                break;
        }
    }
}