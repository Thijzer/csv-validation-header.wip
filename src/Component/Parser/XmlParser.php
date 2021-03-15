<?php

namespace Misery\Component\Parser;

use Misery\Component\Common\Cursor\CursorInterface;

class XmlParser implements CursorInterface
{
    public const CONTAINER = 'Container';

    /** @var string */
    private $container;
    private $xml;
    /** @var array|false|mixed|string */
    private $headers;
    /** @var int|null */
    private $count;
    private $i = 0;

    public function __construct(
        string $file,
        string $container = null
    ) {
        $this->container = $container;

        $this->xml = new \XMLReader();
        $this->xml->open($file);
    }

    public static function create(string $filename, string $container = null): self 
    {
        return new self($filename, $container);
    }

    /**
     * {@inheritDoc}
     */
    public function loop(callable $callable): void
    {
        foreach ($this->getIterator() as $row) {
            $callable($row);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator(): \Generator
    {
        while ($this->valid()) {
            yield $this->key() => $this->current();
            $this->next();
        }

        $this->rewind();
    }

    /**
     * {@inheritDoc}
     * @throws Exception\InvalidCsvElementSizeException
     * @return false|array
     */
    public function current()
    {
        while($this->i === 0 && $this->xml->read() && $this->xml->name != $this->container) {
            ;
        }
        $this->i++;

        if ($this->xml->name == $this->container) {
            return json_decode(json_encode(new \SimpleXMLElement($this->xml->readOuterXML())), true);
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function next(): void
    {
        $this->xml->next($this->container);
    }

    /**
     * {@inheritDoc}
     */
    public function key()
    {
        return $this->i;
    }

    /**
     * {@inheritDoc}
     */
    public function valid(): bool
    {
        return false !== $this->current();
    }

    /**
     * {@inheritDoc}
     */
    public function rewind(): void
    {
        if (false === $this->valid()) {
            $this->count = $this->key() - 1;
        }

        $this->count();
        $this->seek(0);

        // move 1 up for the headers
        $this->next();
    }

    /**
     * {@inheritDoc}
     */
    public function seek($pointer): void
    {
        $this->xml->moveToAttributeNo($pointer);
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        if (null === $this->count) {
            $this->loop(function (){});
        }

        return $this->count;
    }
}