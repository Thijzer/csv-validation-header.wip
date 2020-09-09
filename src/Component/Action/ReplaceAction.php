<?php

namespace Misery\Component\Action;

use Misery\Component\Akeneo\AkeneoValuePicker;
use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;
use Misery\Component\Reader\ItemRepositoryAwareInterface;
use Misery\Component\Reader\ItemRepositoryAwareTrait;

// TODO we are stuck on 'code' source -> getReference or SourceRepository in favor of reader are other options

class ReplaceAction implements OptionsInterface, ItemRepositoryAwareInterface
{
    use OptionsTrait;
    use ItemRepositoryAwareTrait;
    private $repo;

    public const NAME = 'replace';

    /** @var array */
    private $options = [
        'method' => null,
        'source' => null,
        'key' => null,
        'format' => '[%s]',
        'reference' => [],
        'content' => 'label',
        'locale' => null,
        'locales' => null,
    ];

    public function apply(array $item): array
    {
        if (array_key_exists($this->options['key'], $item)) {
            $label = $this->options['content'];

            switch ($this->options['method']) {

                case "getLabel":
                    $sourceItem = $this->getItem($listItem = $item[$this->options['key']]);
                    $item[$this->options['key']] = $sourceItem ? AkeneoValuePicker::pick($sourceItem, $label, $this->options) : $this->format($listItem);
                    break;

                case "getLabels":
                    $sourceItem = $this->getItem($listItem = $item[$this->options['key']]);
                    $tmp = [];
                    foreach ($this->options['locales'] as $locale) {
                        $tmp[$locale] = $sourceItem ? AkeneoValuePicker::pick($sourceItem, $label, ['locale' => $locale]) : $this->format($listItem);
                    }
                    $item[$this->options['key']] = $tmp;
                    break;

                case "getLabelFromList":
                    $tmp = [];
                    foreach ($item[$this->options['key']] as $key => $listItem) {
                        $sourceItem = $this->getItem($listItem);
                        $tmp[$key] = $sourceItem ? AkeneoValuePicker::pick($sourceItem, $label, $this->options) : $this->format($listItem);
                    }

                    $item[$this->options['key']] = $tmp;
                    break;

                case "getLabelsFromList":
                    $tmp = [];
                    foreach ($this->options['locales'] as $locale) {
                        $tmp[$locale] = [];
                        foreach ($item[$this->options['key']] as $listItem) {
                            $sourceItem = $this->getItem($listItem);
                            $tmp[$locale][] = $sourceItem ? AkeneoValuePicker::pick($sourceItem, $label, ['locale' => $locale]) : $this->format($listItem);
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

    private function format(string $item = null)
    {
        return $item && $this->options['format'] ? sprintf($this->options['format'], $item) : null;
    }

    private function getItem($reference)
    {
        if (null === $reference) {
            return null;
        }

        $references = [$reference];
        if ($this->options['guide']) {
            // find matching key position
            foreach ($this->options['guide'] as $key => $ref) {
                if ($nKey = array_flip($this->getRepository()->getReferences())[$key] ?? false) {
                    $references[$nKey] = $ref;
                }
            }
        }

        return $this
            ->getRepository()
            ->find(...$references)
        ;
    }
}