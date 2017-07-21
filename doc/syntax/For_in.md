# For ... in ...

Alternative syntax for a `foreach` loop in PHP:
```php
<?php

$items = ['foo', 'bar', 'baz'];

foreach ($items as $item) {
    echo $item . ', ';
}
// Will output: foo, bar, baz,
```

The `for ... in ...` loop expects first a variable which will be available inside the loop body and then the array/object to loop over it:

```php
<?phx

$items = ['foo', 'bar', 'baz'];

for ($item in $items) {
    echo $item . ', ';
}
// Will output: foo, bar, baz,
```