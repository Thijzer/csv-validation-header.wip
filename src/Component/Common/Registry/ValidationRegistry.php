<?php

namespace Misery\Component\Common\Registry;

use Misery\Component\Common\Collection\ArrayCollection;
use Misery\Component\Validator\ValidatorInterface;

class ValidationRegistry implements Registry
{
    public const NAME = 'validation';

    private $validators;

    public function __construct()
    {
        $this->validators = new ArrayCollection();
    }

    public function register(ValidatorInterface $validator): self
    {
        $this->validators->set($validator::NAME, $validator);

        return $this;
    }

    public function filterByName($name): ArrayCollection
    {
        return $this->validators->get($name);
    }
}