### Format
The formatting Component are none destructive, meaning that you always have a reverseFormat option to return to it's original value.

## Interface

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