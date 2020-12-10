<?php

namespace Misery\Component\Source;

use Misery\Component\Common\Cursor\CachedCursor;
use Misery\Component\Common\Cursor\FunctionalCursor;
use Misery\Component\Common\Repository\ItemRepository;
use Misery\Component\Decoder\ItemDecoder;
use Misery\Component\Encoder\ItemEncoder;
use Misery\Component\Parser\CsvParser;
use Misery\Component\Reader\ItemReader;
use Misery\Component\Reader\ItemReaderInterface;

class Source
{
    private $input;
    private $reader;
    private $alias;
    /** @var ItemEncoder */
    private $encoder;
    /** @var ItemDecoder */
    private $decoder;
    /** @var array */
    private $configuration;
    /** @var ItemRepository */
    private $repository;

    public function __construct(
        ItemEncoder $encoder,
        ItemDecoder $decoder,
        array $configuration,
        string $input,
        string $alias
    ) {
        $this->input = $input;
        $this->alias = $alias;
        $this->encoder = $encoder;
        $this->decoder = $decoder;
        $this->configuration = $configuration;
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

    public function getRepository(): ItemRepository
    {
        if (null === $this->repository) {
            $this->repository = new ItemRepository(
                $this->getReader(),
                $this->configuration['parse']['reference']
            );
        }

        return $this->repository;
    }

    public function getReader(): ItemReaderInterface
    {
        if (null === $this->reader) {
            if ($this->configuration['parse']['type'] === 'csv') {

                $format = $this->configuration['parse']['format'];

                $this->reader = new ItemReader(
                    new CachedCursor(
                        new FunctionalCursor(
                            CsvParser::create(
                                $this->input,
                                $format['delimiter'],
                                $format['enclosure']
                            ), function ($item) {
                            return $this->encode($item);
                        }
                        ),
                        [
                            'cache_size' => CachedCursor::LARGE_CACHE_SIZE,
                        ]
                    )
                );
            }
        }

        return $this->reader;
    }
}