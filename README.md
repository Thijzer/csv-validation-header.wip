# Item processor

## require this into your project 
```shell script
composer require thijzer/csv-validation-header
```

## Documentation
You can find the documentation in the `/docs` directory.

## Development

### Before you start
This branch is using docker for development.
```shell script
alias d_composer="docker-compose exec fpm php -d memory_limit=-1 /usr/bin/composer $1"
```
### Installation and testing for development
```shell script
d_composer install -o 
d_composer test 
```