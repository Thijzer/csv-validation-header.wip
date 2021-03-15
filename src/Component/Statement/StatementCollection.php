<?php

namespace Misery\Component\Statement;

use Misery\Component\Common\Collection\ArrayCollection;

class StatementCollection extends ArrayCollection
{
    public function apply(array $item): array
    {
        /** @var StatementInterface $statement */
        foreach ($this->getValues() as $statement) {
            $item = $statement->apply($item);
        }

        return $item;
    }

}