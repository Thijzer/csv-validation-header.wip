<?php

namespace Misery\Component\Converter;

use Misery\Component\Akeneo\Header\AkeneoHeader;
use Misery\Component\Akeneo\Header\AkeneoHeaderTypes;
use Misery\Component\Akeneo\StandardValueCreator;
use Misery\Component\Common\Cursor\CachedCursor;
use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;
use Misery\Component\Common\Pipeline\Exception\InvalidItemException;
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
        'akeneo-types' => [],
        'locales' => [],
    ];

    private $types = [];

    /** @var ProductStructuredDataToAkeneoApiConverter */
    private $reverter;
    /** @var StandardValueCreator */
    private $valueCreator;
    /** @var \Misery\Component\Reader\ItemReaderInterface */
    private $cachedReader;

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

            $this->cachedReader = $this
                ->getConfiguration()
                ->getSources()
                ->get($this->getOption('attribute_options_file'))
                ->getCachedReader(['cache_size' => CachedCursor::EXTRA_LARGE_CACHE_SIZE])
            ;

            $this->reverter = new ProductStructuredDataToAkeneoApiConverter();
            $this->reverter->setOption('attributes:list', $this->getOption('attributes:list'));
            $this->reverter->setOption('localizable_codes:list', $this->getOption('localizable_codes:list'));

            $this->types = $this->getOption('akeneo-types');

            $this->valueCreator = new StandardValueCreator();
        }

        $akeneoMapping = $this->getOption('akeneo-mapping:list');

        $output = [];
        $invalid_msgs = [];
        foreach ($itemCollection as $item) {
            $context = $this->header->getContext($item['UID']);

            if (empty($item['VALUE_NL'])) {
                continue;
            }
            // we deal with prices in file not API
            if ($context['type'] === AkeneoHeaderTypes::PRICE) {
                continue;
            }

            if ($this->types !== [] && !in_array($context['type'], $this->types)) {
                continue;
            }
            $output['identifier'] = $item['SKU'];

            unset($item['SKU']);

            if ($context['type'] === AkeneoHeaderTypes::TEXT) {
                $output['values'][$item['UID']][] = $this->valueCreator->create($item['UID'], $item['VALUE_NL']);
                continue;
            }

            if ($context['type'] === AkeneoHeaderTypes::SELECT) {
                $value = $this->findSelectCode($item['UID'], $item['VALUE_NL']);
                if (null === $value) {

                    $invalid_msgs[] = sprintf('Unable to find an option code for  %s : %s', $item['UID'], $item['VALUE_NL']);
                    continue;
                }
                $output['values'][$item['UID']][] = $this->valueCreator->create($item['UID'], $value);
                continue;
            }

            if (in_array($context['type'], [AkeneoHeaderTypes::METRIC, 'pim_catalog_metric_as400']) && $item['VALUE_NL']) {

                // metrics need a unit
                $unit = !empty($item['UOM']) ? $akeneoMapping[$item['UOM']] ?? null : null;
                if (empty($unit) && $item['VALUE_NL'] === '-') {
                    $unit = '_';
                }
                if (empty($unit)) {
                    // disabled on customer request
                    $unit = '_';
                    //$invalid_msgs[] = sprintf('metric code %s has no unit', $item['UID']);
                    //continue;
                }

                $output['values'][$item['UID']][] = $this->valueCreator->createUnit($item['UID'], $unit, $item['VALUE_NL']);
                continue;
            }

            if ($context['type'] === AkeneoHeaderTypes::MULTISELECT) {
                $list = [];
                foreach (array_filter(explode(';;', $item['VALUE_NL'])) as $value) {
                    $select = $this->findSelectCode($item['UID'], $value);
                    if (null === $select) {
                        $invalid_msgs[] = sprintf('Unable to find a option code for  %s : %s', $item['UID'], $value);
                        continue;
                    }
                    // remove doubles
                    $list[] = $select;
                }
                if ($list !== []) {
                    $output['values'][$item['UID']][] = $this->valueCreator->createArrayData($item['UID'], array_unique($list));
                }
                continue;
            }
        }

        if (count($invalid_msgs) > 0) {
            throw new InvalidItemException(implode(', ', $invalid_msgs), $itemCollection);
        }

        return $output;
    }

    public function findSelectCode(string $code, string $value)
    {
        return $this->cachedReader->find(['attribute' => $code, 'label-nl_BE' => $value])->getIterator()->current()['code'] ?? null;
    }

    public function revert(array $item): array
    {
        $item = $this->reverter->revert($item);

        return $item;
    }

    public function getName(): string
    {
        return 'as400/article-attributes/api';
    }
}