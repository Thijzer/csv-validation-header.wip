# Encoders

# Examples
```php
$parser = CsvParser::create(__DIR__ . '/families.csv', ';');
$encoder = new CsvEncoder($formatRegistry, $modifierRegistry);

$context = Symfony\Component\Yaml\Yaml::parseFile(__DIR__ . '/akeneo/validation/families.yaml');

// iterate data
foreach ($parser->getIterator() as $row) {
    $encodedData = $encoder->encode($row, $context);
}
```