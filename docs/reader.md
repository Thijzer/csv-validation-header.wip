# Reader

## Reader Interface

```php
    public function find(array $constraints): self;
    public function filter(callable $callable): self;
    public function getIterator(): \Iterator;
    public function getValues(): array;
```

RowReader
```php
    public function getRow(int $line): self;
    public function getRows(array $lines): self;
    public function getColumns(string...$columnNames): self;
```

## examples

Here we get id and name from user 'Nick'

```php
$parser = CsvParser::create(__DIR__ . '/users.csv');
$reader = new CsvReader($parser);

$reader
    ->getColumns('id', 'name')
    ->find(['users' => 'Nick'])
    ->getValues()
;
```

here we get id and name from first 100 lines

```php
$parser = CsvParser::create(__DIR__ . '/users.csv');
$reader = new CsvReader($parser);

$reader
    ->getColumns('id', 'name')
    ->getRows(range(1, 100))
    ->getValues()
;
```

Here we get id and name from user 'Nick' using the filter method

```php
$parser = CsvParser::create(__DIR__ . '/users.csv');
$reader = new CsvReader($parser);

$reader
    ->getColumns('first_name', 'last_name')
    ->filter(function ($row) {
        return $row['first_name'] === 'Nick';
    })
    ->getValues()
;
```