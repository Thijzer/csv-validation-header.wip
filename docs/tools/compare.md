# Compare tool

When compare files, old with new, we need to comply with a couple of situations.
First we need to to know what has changed, and these need to be marked.
The Compare tool should work for all parsable files

## Interface

```php
public function compare(string...$references): array
```

## References
The reference(s) here are the unique identifiers inside the file.
Without reference you cannot compare A with B.
Some files have multiple or joined references, so uniqueness is based on both references.

## example

```php
$parserA = CsvParser::create(__DIR__ . '/products_aug.csv');
$parserB = CachedCursor::create(CsvParser::create(__DIR__ . '/products_set.csv'));

$compare = new Misery\Component\Csv\Compare\ItemCompare(
    $parserA,
    $parserB
);

$compare->compare('sku');
```

The result as array structure with indications of what lines are added, changed and removed.
And the changed lines show before and after.
