<?php

namespace Misery\Component\Source\Command;

use Misery\Component\Common\Options\OptionsTrait;
use Misery\Component\Common\Registry\RegisteredByNameInterface;
use Misery\Component\Reader\ReaderInterface;
use Misery\Component\Source\SourceAwareInterface;
use Misery\Component\Source\SourceTrait;
use Misery\Component\Statement\StatementBuilder;
use Misery\Component\Statement\StatementCollection;
use Misery\Component\Statement\StatementFactory;
use mysql_xdevapi\Statement;

class SourceFilterCommand implements ExecuteSourceCommandInterface, SourceAwareInterface, RegisteredByNameInterface
{
    use SourceTrait;
    use OptionsTrait;

    private $reader;

    private $options = [
        'return_value' => null,
        'cache' => [],
        'filter' => null,
        'criteria' => null,
        'statement' => null,
    ];

    public function execute()
    {
        if ($this->reader === null) {
            $this->reader = $this->getSource()->getCachedReader($this->getOption('cache'));
        }

        if ($this->getOption('statement')) {
            $collection = new StatementCollection(
                StatementBuilder::fromArray(
                    $this->getOption('statement')
                )
            );

            $this->reader = $this->reader->filter(function ($item) use ($collection) {
                return $collection->isApplicable($item);
            });
        }

        $items = $this->reader;
        if ($this->getOption('criteria')) {
            $items = $this->reader->find($this->getOption('criteria'));
        }

        if (!empty($this->getOption('return_value'))) {
            return array_map(function (array $item) {
                return $item[$this->getOption('return_value')];
            }, $items->getItems());
        }

        return $items;
    }

    public function executeWithOptions(array $options)
    {
        $staleOptions = $this->getOptions();

        $this->setOptions($options);

        $result = $this->execute();

        $this->setOptions($staleOptions);

        return $result;
    }

    public function getName(): string
    {
        return 'filter';
    }
}