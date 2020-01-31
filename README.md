# csv-validation-header.RFC

# see the docs for documentation

## 2 Value processing patterns
We have 2 distinct ways to format our values. Destructive and none destructive.
It's very important that you know that difference because you can not recover for a destructive pattern.

The Csv File is a file that creates adds more Contextual elements like 
- references
- delimiter, enclosure, escapeChar
- validation rules




## RowReader Interface
```php
interface RowReader
{
    public function getRow(int $line): self;
    public function getRows(array $lines): self;
    public function getColumns(string...$columnNames): self;
}
```
RowReaderInterface remains and issue
it stricts lots of other classes into the CSV kind

=> getRow getRows =>=> index

get columns is only used for getValues from a specific column like sku
and can be replaced by ColumnValuesFetcher helper
=> getColumns deprecaded


## Tests
```shell script
alias d_composer = docker-compose exec  fpm php -d memory_limit=-1 composer.phar $1
d_composer test 
```