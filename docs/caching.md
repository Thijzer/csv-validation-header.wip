# Caching

Caching is very important in a lot of cases. We will explain this with in full detail in the example section.
We also have different kinds of caching options, each the specific use case.

The main principle of caching is simple, it's designed to be transparent en you can use it whenever you need it.
The Caching layer is complaint with the Parser Interface so you can literally slip it between things.

```php
// not cached parser
$parser = CsvParser::create(__DIR__ . '/family.csv');
// cached parser
$parser = CachedCursor::create($parser);
```

Here is an example where caching can be useful.
```php
$parserA = CsvParser::create(__DIR__ . '/products_aug.csv');
$parserB = CachedCursor::create(CsvParser::create(__DIR__ . '/products_set.csv'));

$compare = new Misery\Component\Csv\Compare\ItemCompare(
    $parserA,
    $parserB
);

$compare->compare('sku');
```
when comparing A on B, file A will be read line by line, will file B will be use to find a line with match 'sku'.
Without caching this is 100x slower because we find the corresponding line meaning file B is read starting from 0.
By adding a cache we add a buffer that will read the first X amount of lines in memory buffer so the compare tool will be reading from memory.
We remain compatible with the Parser interface so the compare to has no clue that it's reading from a buffer.

### RedisCache
Another important caching mechanism is redis caching, redis caching is meant for FileRepositories.
Imaging a database kind of file repository where you want to quick compare or find the file.
Instead of reading this file line by line (slow), we can read a cached version that is stored inside redis.

The redis cache is still under development so I can't share to much details as of yet.
But the goal is that the md5 hash a key file that can be fetched from redis, the file repo will consist of buffered fragments of the file.
So if our buffer amount is 1.000 and we are storing a 100.000 line product import that will consist of 100 buffered items for that file.
