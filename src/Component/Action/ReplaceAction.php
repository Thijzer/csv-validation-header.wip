<?php

namespace Misery\Component\Action;

use Misery\Component\Akeneo\AkeneoValuePicker;
use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;
use Misery\Component\Reader\ItemReaderAwareInterface;
use Misery\Component\Reader\ItemReaderAwareTrait;

// TODO we are stuck on 'code' source -> getReference or SourceRepository in favor of reader are other options

class ReplaceAction implements OptionsInterface, ItemReaderAwareInterface
{
    use OptionsTrait;
    use ItemReaderAwareTrait;
    private $repo;

    public const NAME = 'replace';

    /** @var array */
    private $options = [
        'method' => null,
        'source' => null,
        'key' => null,
        'reference' => 'code',
        'content' => 'label',
        'locales' => null,
    ];

    public function apply(array $item): array
    {
        if (isset($item[$this->options['key']])) {
            $label = $this->options['content'];

            switch ($this->options['method']) {

                case "getLabel":
                    $sourceItem = $this->getItem($item[$this->options['key']]);
                    $item[$this->options['key']] = AkeneoValuePicker::pick($sourceItem, $label, $this->options);
                    break;

                case "getLabels":
                    $sourceItem = $this->getItem($item[$this->options['key']]);
                    $tmp = [];
                    foreach ($this->options['locales'] as $locale) {
                        $tmp[$locale] = AkeneoValuePicker::pick($sourceItem, $label, ['locale' => $locale]);
                    }
                    $item[$this->options['key']] = $tmp;
                    break;

                case "getLabelFromList":
                    $tmp = [];
                    foreach ($item[$this->options['key']] as $key => $listItem) {
                        $sourceItem = $this->getItem($listItem);
                        $tmp[$key] = AkeneoValuePicker::pick($sourceItem, $label, $this->options);
                    }

                    $item[$this->options['key']] = $tmp;
                    break;

                case "getLabelsFromList":
                    $tmp = [];
                    foreach ($this->options['locales'] as $locale) {
                        $tmp[$locale] = [];
                        foreach ($item[$this->options['key']] as $listItem) {
                            $sourceItem = $this->getItem($listItem);
                            $tmp[$locale][] = AkeneoValuePicker::pick($sourceItem, $label, ['locale' => $locale]);
                        }
                    }
                    $item[$this->options['key']] = $tmp;
                    break;

                default;
                    break;
            }
        }

        return $item;
    }

    private function getItem($reference)
    {
        return $this
            ->getReader()
            ->find([$this->options['reference'] => $reference])
            ->getIterator()
            ->current()
        ;
    }
}