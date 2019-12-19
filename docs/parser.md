# Parser

## Interface

```php
    /**
     * Iterate over items with a Generator and do something
     *
     * @param callable $callable
     */
    public function loop(callable $callable): void;

    /**
     * Iterate over items with a Generator
     *
     * @return \Generator
     */
    public function getIterator(): \Generator;
```

## Description
A parsable file needs to be compliant with the Parsable Interface.
This interface is based on the Generator pattern. Meaning it will return a parsable item in a loop.
You can also loop with a callback, this can be handy process the item directly.
This Parsers are memory save so no worries on parsing millions of records.

Here we make a copy of the file
```php

$parser = CsvParser::create(__DIR__ . '/family.csv');
$csvWriter = new CsvWriter(__DIR__ . '/family_copy.csv');

$parser->loop(function ($row) use ($csvWriter) {
    $csvWriter->write($row);
});
```

Here is an example of the iterator
```php
$parser = CsvParser::create(__DIR__ . '/family.csv');

foreach ($parser->getIterator() as $row) {
    $encodedData = $encoder->encode($row, $context);
}
```