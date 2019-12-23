# Decoder

Decoding is the reverse of encoding.
Some encoders options can be destructive, meaning we can't revert those changes.
So be careful which modifiers you connect during encoding.

> # encoding
> Csv => structured item
> # decoding
> structured item => CSV

# Examples

```php
$parser = CsvParser::create(__DIR__ . '/families.csv', ';');
$decoder = new CsvDecoder($formatRegistry, $modifierRegistry);

$context = Yaml::parseFile(__DIR__ . '/families.yaml');

foreach ($parser->getIterator() as $row) {
    $decodedData = $decoder->decode($row, $context);
}
```

This could be a valid families.yaml

```yaml
encoding:
  cell:
    code:
      string: ~
    attributes:
      list: ~
  row:
    unflatten:
      separator: '-'
    nullify: ~

format:
  delimiter: ';'
  enclosure: '"'
  reference: 'code'
```

Before 

```json
{
   "code": "auto",
   "label-en_US": "auto",
   "label-nl_BE": "car",
   "attributes": "description,maximum_frame_rate,maximum_video_resolution"
}
```

After

```json
{
   "code": "auto",
   "label": {
      "en_US": "auto",
      "nl_BE": "car"
    },
   "attributes": ["description","maximum_frame_rate","maximum_video_resolution"]
}
```