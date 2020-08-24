<?php

namespace Misery\Component\Source;

use Misery\Component\Common\Cursor\CachedCursor;
use Misery\Component\Common\Cursor\FunctionalCursor;
use Misery\Component\Decoder\ItemDecoder;
use Misery\Component\Encoder\ItemEncoder;
use Misery\Component\Parser\CsvParser;
use Misery\Component\Reader\ItemReader;
use Misery\Component\Reader\ItemReaderInterface;

class Source
{
    private $type;
    private $input;
    private $readers;
    private $alias;
    /** @var ItemEncoder */
    private $encoder;
    /** @var ItemDecoder */
    private $decoder;

    public function __construct(
        SourceType $type,
        ItemEncoder $encoder,
        ItemDecoder $decoder,
        string $input,
        string $alias
    ) {
        $this->type = $type;
        $this->input = $input;
        $this->alias = $alias;
        $this->encoder = $encoder;
        $this->decoder = $decoder;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function encode($item)
    {
        return $this->encoder->encode($item);
    }

    public function decode($item)
    {
        return $this->decoder->decode($item);
    }

    public function getReader(): ItemReaderInterface
    {
        if (false === isset($this->readers[$this->input])) {
            if ($this->type->is('file')) {
                $this->readers[$this->input] = new ItemReader(new FunctionalCursor(new CachedCursor(CsvParser::create($this->input)), function($item) {
                    return $this->encode($item);
                }));
            }
        }

        return $this->readers[$this->input];
    }
}