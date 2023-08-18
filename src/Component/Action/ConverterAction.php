<?php

namespace Misery\Component\Action;

use Misery\Component\AttributeFormatter\AttributeValueFormatter;
use Misery\Component\AttributeFormatter\BooleanLabelsAttributeFormatter;
use Misery\Component\AttributeFormatter\MetricAttributeFormatter;
use Misery\Component\AttributeFormatter\NumberAttributeFormatter;
use Misery\Component\AttributeFormatter\PriceCollectionFormatter;
use Misery\Component\AttributeFormatter\PropertyFormatterRegistry;
use Misery\Component\Common\Functions\ArrayFunctions;
use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;
use Misery\Component\Configurator\ConfigurationAwareInterface;
use Misery\Component\Configurator\ConfigurationTrait;

class ConverterAction implements OptionsInterface, ConfigurationAwareInterface
{
    use OptionsTrait;
    use ConfigurationTrait;

    public const NAME = 'converter';

    /** @var array */
    private $options = [
        'name' => null,
    ];

    public function apply(array $item): array
    {
        $converterName = $this->getOption('name');
        if (null === $converterName) {
            return $item;
        }

        $converter = $this->configuration->getConverter($converterName);

        return $converter ? $converter->convert($item): $item;
    }
}