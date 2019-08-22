<?php

namespace Misery\Component\Common\Registry;

use Misery\Component\Common\Collection\ArrayCollection;
use Misery\Component\Common\Format\Format;

class FormatRegistry implements Registry
{
    public const NAME = 'format';

    private $formatters;

    public function __construct()
    {
        $this->formatters = new ArrayCollection();
    }

    public function register(Format $format): self
    {
        $this->formatters->set(\get_class($format), $format);

        return $this;
    }

    public function filterByName($name): ArrayCollection
    {
        return $this->formatters->filter(static function (Format $format) use ($name) {
            return $format::NAME === $name;
        });
    }
}