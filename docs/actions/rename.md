# Rename Action

rename is only allowed for columns
If you wish to change values use the Value modifiers

## examples

simple rename

```yaml
  rename:
    -
      type: column
      mapping:
        - identifier: code
```

Rename with regex

```yaml
  rename:
    -
      type: column
      find: label*
      mapping:
        - nl: nl_BE
        - fr: nl_FR
```
