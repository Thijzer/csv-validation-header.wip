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
        'locales' => null,
    ];

    public function apply(array $item): array
    {
        if (isset($item[$this->options['key']])) {
            if ($this->options['method'] === 'getLabel') {

                // Todo needs improvement?
                if ($sourceItem = $this->getItem($item[$this->options['key']])) {

                    if (is_array($this->options['locales'])) {
                        $tmp = [];
                        foreach ($this->options['locales'] as $locale) {
                            $tmp[$locale] = AkeneoValuePicker::pick($sourceItem, 'label', ['locale' => $locale]);
                        }
                        $item[$this->options['key']] = $tmp;

                    } else {
                        $item[$this->options['key']] = AkeneoValuePicker::pick($sourceItem, 'label', $this->options);
                    }
                }
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