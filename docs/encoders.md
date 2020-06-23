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

```yaml
colour-nl_BE;colour-en_GB
red;rood

deflatten:
  sep: '-'

colour:
  nl_BE: red
  fr_BE: rood

# reverse
flatten:
  sep: '-'
```
