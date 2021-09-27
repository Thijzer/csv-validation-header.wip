<?php

namespace Misery\Component\Reader;

use Misery\Component\Common\Cursor\CursorInterface;
use Misery\Component\Common\Cursor\SubFunctionalCollectionCursor;
use Misery\Component\Common\Functions\ArrayFunctions;
use Misery\Component\Common\Registry\RegisteredByNameInterface;

class ItemReaderFactory implements RegisteredByNameInterface
{
    private $collection = [];
    public function createFromConfiguration(CursorInterface $cursor, array $configuration): ItemReaderInterface
    {
        if (isset($configuration['x_filter'])) {
            return new ItemReader(new SubFunctionalCollectionCursor($cursor, function ($item) {
                // we need a reader that can accept cursor in cursor similar to the SubCursor
                // why? contextually we need the full item to create the correct sub-items
                // from that item we yield inside sub items
                // So we return new ItemCollection() that needs to be depleted, finally we will return false.

                // what I'm describing is not a filter but a converter that returns an ItemCollection not an item
                $itemCol = new ItemCollection();
                foreach (ArrayFunctions::unflatten($item, ' ')['Attribuut'] as $itemValue) {
                    $key = $itemValue['ID'];
                    $label = $itemValue['Label'];

                    if ($key && !isset($this->collection[$key])) {
                        $this->collection[$key] = $key;
                        $itemCol->set($key, [
                            'code' => 'klium_'.$key,
                            'label-nl_BE' => $label,
                        ]);
                    }
                }

                return $itemCol;
            }));
        }

        $reader = new ItemReader($cursor);
        if (isset($configuration['filter'])) {
            $reader = $reader->find($configuration['filter']);
        }

        if (isset($configuration['sort'])) {
            $reader = $reader->sort($configuration['sort']);
        }

        return $reader;
    }

    public function getName(): string
    {
        return 'reader';
    }
}