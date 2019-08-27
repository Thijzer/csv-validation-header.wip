<?php

namespace Misery\Component\Common\Registry;

use Misery\Component\Common\Collection\ArrayCollection;
use Misery\Component\Common\Format\Format;
use Misery\Component\Csv\Reader\ReaderInterface;
use Misery\src\Component\Validator\ValidatorInterface;

class ReaderRegistry implements Registry
{
    public const NAME = 'reader';

    private $readers;

    public function __construct()
    {
        $this->readers = new ArrayCollection();
    }

    public function register(ReaderInterface $reader, string $alias): self
    {
        $this->readers->set($alias, $reader);

        return $this;
    }

    public function filterByName($alias): ArrayCollection
    {
        return $this->readers->get($alias);
    }
}