<?php

namespace Misery\Component\BluePrint;

use Misery\Component\Converter\ConverterInterface;
use Misery\Component\Decoder\ItemDecoder;
use Misery\Component\Encoder\ItemEncoder;

class BluePrint
{
    private $name;
    private $encoder;
    private $decoder;
    private $converter;

    public function __construct(
        string $name,
        ItemEncoder $encoder,
        ItemDecoder $decoder,
        ConverterInterface $converter = null
    ) {
        $this->encoder = $encoder;
        $this->decoder = $decoder;
        $this->name = $name;
        $this->converter = $converter;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEncoder(): ItemEncoder
    {
        return $this->encoder;
    }

    public function getConverter(): ?ConverterInterface
    {
        return $this->converter;
    }

    public function getDecoder(): ItemDecoder
    {
        return $this->decoder;
    }
}