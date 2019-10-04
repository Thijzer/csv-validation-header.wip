# csv-validation-header.RFC


## CSV Filter

### how filterBy works atm

$data->findBy(['user' => 'Patrick'])

1. fetch column('user') => [1 => 'John', 2 => 'Patrick'] (column sequence is cached)
2. from fetch subtract the line numbers.
3. now fetch and return rows from those lines

## 2 Value processing patterns
We have 2 distinct ways to format our values. Destructive and none destructive.
It's very important that you know that difference because you can not recover for a destructive pattern.

### Modifier
The modifier Component is a destructive pattern, meaning that it's value is unrecoverable to it's original state.
Modifier user are the last phase in the process. 

### Format
The formatting Component are none destructive, meaning that you always have a reverseFormat option to return to it's original value.

```php
    public function format(string $value): int
    {
        return (int) $value;
    }
```

#### Reverse format
You are able to recover from a formatted value, but only if you record that action.

```php
    public function reverseFormat(int $value): string
    {
        return (string) $value;
    }
```

#### Csv File
The Csv File is a file that creates adds more Contextual elements like 
- references
- delimiter, enclosure, escapeChar
- validation rules


#### Reader Interface

Tool that simplifies the reading of files.


```php

$reader = new CsvReader($cursor);

$reader->getColumns('id', 'name')->find(['name' => 'Nick'])->geValues();

$reader->getColumns('id', 'name')->getRows(range(1, 100))->geValues();

```
