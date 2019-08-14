# csv-validation-header.RFC


### how filterBy works atm

$data->findBy(['user' => 'Patrick'])

1. fetch column('user') => [1 => 'John', 2 => 'Patrick'] (column sequence is cached)

2. from fetch subtract the line numbers.

3. now fetch and return rows from those lines
