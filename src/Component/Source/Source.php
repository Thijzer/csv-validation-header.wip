<?php

namespace Misery\Component\Source;

use Misery\Component\Item\Processor\NullProcessor;
use Misery\Component\Item\Processor\ProcessorInterface;
use Misery\Component\Reader\ItemReaderInterface;

class Source
{
    private $reader;
    private $alias;
    private $processorIn;
    private $processorOut;

    public function __construct(
        ItemReaderInterface $reader,
        ProcessorInterface $processorIn,
        ProcessorInterface $processorOut,
        string $alias
    ) {
        $this->alias = $alias;
        $this->reader = $reader;
        $this->processorIn = $processorIn;
        $this->processorOut = $processorOut;
    }

    public static function createSimple(ItemReaderInterface $reader, string $alias): self
    {
        return new self($reader, new NullProcessor(), new NullProcessor(), $alias);
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function encode(array $item): array
    {
        return $this->processorIn->process($item);
    }

    public function decode(array $item): array
    {
        return $this->processorOut->process($item);
    }

    public function getReader(): ItemReaderInterface
    {
        return $this->reader;
    }
}