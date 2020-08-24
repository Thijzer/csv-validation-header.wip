<?php

namespace Misery\Component\Action;

class ItemActionProcessor
{
    private $configurationRules;

    public function __construct(array $configurationRules)
    {
        $this->configurationRules = $configurationRules;
    }

    public function process(array $item): array
    {
        foreach ($this->configurationRules as $name => $action) {
            $item = $action->apply($item);
        }

        return $item;
    }
}