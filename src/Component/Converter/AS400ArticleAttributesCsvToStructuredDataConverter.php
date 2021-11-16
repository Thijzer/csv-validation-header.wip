<?php

namespace Misery\Component\Converter;

use Misery\Component\Akeneo\Header\AkeneoHeader;
use Misery\Component\Akeneo\Header\AkeneoHeaderTypes;
use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;
use Misery\Component\Common\Registry\RegisteredByNameInterface;
use Misery\Component\Configurator\ConfigurationAwareInterface;
use Misery\Component\Configurator\ConfigurationTrait;
use Misery\Component\Modifier\ReferenceCodeModifier;
use Misery\Component\Modifier\StringToLowerModifier;

class AS400ArticleAttributesCsvToStructuredDataConverter implements ConverterInterface, OptionsInterface, RegisteredByNameInterface, ConfigurationAwareInterface
{
    use OptionsTrait;
    use ConfigurationTrait;

    /** @var AkeneoHeader */
    private $header;
    private $options = [
        'attributes:list' => null,
        'localizable_codes:list' => null,
        'akeneo-mapping:list' => null,
        'locales' => null,
    ];

    public function convert(array $itemCollection): array
    {
        if (null === $this->header) {

            // akeneo mapping filter
            $this->setOption('akeneo-mapping:list', array_map(function($listItem) {
                $path = pathinfo($listItem);
                if ($path['extension'] == 'standard') {
                    return $listItem;
                }
                return $path['extension'];
            }, $this->getOption('akeneo-mapping:list')));

            // header Factory
            $this->header = (new AS400ArticleAttributesHeaderContext())->create(
                $this->getOption('attributes:list'),
                $this->getOption('localizable_codes:list'),
                $this->getOption('locales')
            );
        }

        $akeneoMapping = $this->getOption('akeneo-mapping:list');

        $output = [];
        foreach ($itemCollection as $item) {
            $context = $this->header->getContext($item['ATTRIBUTE_CODE']);
            $output['sku'] = $item['SKU'];

            if ($context['has_locale'] === true && $context['type'] === AkeneoHeaderTypes::METRIC && $item['ATTRIBUTE_UNIT']) {
                dump($context, $item);exit;
                continue;
            }

            if ($context['type'] === AkeneoHeaderTypes::SELECT || $context['type'] === AkeneoHeaderTypes::MULTISELECT) {
                $item['ATTRIBUTE_VALUE'] = $this->convertToSelectCode($item['ATTRIBUTE_CODE'], $item['ATTRIBUTE_VALUE']);
                // ASSUMPTION: the french value is always the dutch value with a french label
                $item['ATTRIBUTE_VALUE_FR'] = $item['ATTRIBUTE_VALUE'];
            }

            if ($context['type'] === AkeneoHeaderTypes::METRIC && $item['ATTRIBUTE_UNIT']) {
                $unit = $akeneoMapping[$item['ATTRIBUTE_UNIT']] ?? null;
                if (null === $unit) {
                    continue;
                }
                $output[$this->header->createItemHeader($item['ATTRIBUTE_CODE'], ['extra' => 'unit'])] = $unit;
            }

            if ($context['has_locale'] === true) {
                $output[$this->header->createItemHeader($item['ATTRIBUTE_CODE'], ['locale' => 'nl_BE'])] = $item['ATTRIBUTE_VALUE'];
                $output[$this->header->createItemHeader($item['ATTRIBUTE_CODE'], ['locale' => 'fr_BE'])] = $item['ATTRIBUTE_VALUE_FR'];
            }
            if ($context['has_locale'] === false) {
                $output[$item['ATTRIBUTE_CODE']] = $item['ATTRIBUTE_VALUE'];
            }
        }

        return $output;
    }

    public function convertToSelectCode(string $code, string $value)
    {
        $refCode = new ReferenceCodeModifier();
        $strtolower = new StringToLowerModifier();

        return $strtolower->modify($refCode->modify($code.' '.$value));
    }

    public function revert(array $item): array
    {
        return array_replace($this->header->getHeaders(), $item);
    }

    public function getName(): string
    {
        return 'as400/article-attributes';
    }
}