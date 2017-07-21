# Spread operator

PHP already knows the spread operator for unpacking arguments from an array for a method call:

```php
<?php

$args = ['foo', 'bar', 'baz'];

$obj = new Object(...$args);
// Same as: $obj = new Object('foo', 'bar', 'baz');
```

PHX increases the support for the spread operator. The usability in arrays is allowed to:

```php
<?phx

$names = ['John', 'Mary'];
$otherNames = ['Jason'];

$allNames = [...$names, ...$otherNames];
// Will result in an array with values: John, Mary, Jason
```

It's also possible to spread in function or method calls which return arrays:
  
```php
<?phx

function getNumbers()
{ 
    return [1, 2, 3];
}

$ids = [0, ...getNumbers(), 4];
// Will result in an array with values: 0, 1, 2, 3, 4

```