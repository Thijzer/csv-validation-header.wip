# copy tool

We don't have a official copy tool, it's not really needed actually.
It's better to connect reader and writer to each other and do functional stuff in between.
This way different combo's are possible and the code remains understandable

## examples

Here we make a copy of the same file
```php

$parser = CsvParser::create(__DIR__ . '/family.csv');
$csvWriter = new CsvWriter(__DIR__ . '/family_copy.csv');

$parser->loop(function ($row) use ($csvWriter) {
    $csvWriter->write($row);
});
```

Here we make a copy of the file but save it in xml

```php

$parser = CsvParser::create(__DIR__ . '/family.csv');
$xmlWriter = new XmlWriter(__DIR__ . '/family_copy.xml');

$parser->loop(function ($row) use ($xmlWriter) {
    $xmlWriter->write($row);
});
```

Here we make a copy of a section of the family csv

```php

$parser = CsvParser::create(__DIR__ . '/family.csv');
$reader = new Reader($parser);
$csvWriter = new CsvWriter(__DIR__ . '/family_led_tvs.csv');

$reader
  ->find(['family' => 'led_tvs', 'display_diagonal' => '26'])
  ->loop(function ($row) use ($csvWriter) {
    $csvWriter->write($row);
});
```