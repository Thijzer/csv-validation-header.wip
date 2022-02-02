<?php

namespace Misery\Component\Reader;

use Misery\Component\Common\Cursor\CondensedCursor;
use Misery\Component\Common\Cursor\CursorInterface;
use Misery\Component\Common\Cursor\SubFunctionalCollectionCursor;
use Misery\Component\Common\Functions\ArrayFunctions;
use Misery\Component\Common\Registry\RegisteredByNameInterface;
use Misery\Component\Filter\ColumnReducer;
use Misery\Component\Item\ItemsFactoryIntoItem;

class ItemReaderFactory implements RegisteredByNameInterface
{
    private $collection = [];

    /**
     * @return ItemReader|ReaderInterface
     */
    public function createFromConfiguration(CursorInterface $cursor, array $configuration)
    {
        if (isset($configuration['x_filter']['type']) && $configuration['x_filter']['type'] === 'collect_unique_attribute_ids') {
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
                            'code' => 'klium_' . $key,
                            'label-nl_BE' => $label,
                        ]);
                    }
                }

                return $itemCol;
            }));
        }

        if (isset($configuration['x_filter']['type']) && $configuration['x_filter']['type'] === 'funnel') {
            $config = $configuration['x_filter'];
            return new ItemReader(new CondensedCursor($cursor, function ($item) use ($config) {
                $key = $item[$config['on']];
                $this->collection[$key][] = $item;

                // new id check
                if (!isset($this->collection['current_id']) || $this->collection['current_id'] !== $key) {
                    $prevId = $this->collection['current_id'] ?? null;
                    $this->collection['current_id'] = $key;

                    if (!$prevId) {
                        return null;
                    }

                    $prev = $this->collection[$prevId];
                    unset($this->collection[$prevId]);

                    if (isset($config['join_into'])) {
                        return ItemsFactoryIntoItem::createFromConfig($prev, $config['join_into']);
                    }

                    return $prev;
                }

                return null;
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