# Item processor

## require this into your project 
```shell script
composer require thijzer/prasable-file-multi-tool
```

## Documentation
You can find the documentation in the `/docs` directory.

## Development

### Before you start
This branch is using docker for development.

### Installation and testing for development
```shell script
bin/composer install -o 

bin/composer unit-test
```

### example commands
```
bin/docker/console transformation --file examples/imes-events-2/transformations_v2_nov/main-insights.yaml --source examples/imes-events-2/sources_v4_nov --workpath examples/imes-events-2/processed_v2
```

```yaml
blueprint:
  validations: <describes the validations>
  parse: <describes the options of interpretation>
  fields: <describes the expected field types>
    sku:
      integer: ~
    enabled:
      bool: ~
    attributes:
      list: ~
    label:
      array: ~
```