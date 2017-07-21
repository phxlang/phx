<?php
if ($argc > 1) {
    var_dump($argv);
}
function getIds()
{
    return [3, 4, 5];
}
class Provider
{
    public function getNames() : array
    {
        return ['Pascal', 'Philipp'];
    }
    public static function getOtherNames() : array
    {
        return ['Cassandra'];
    }
}
// Results in array: ['foo', 'hello', 'my', 'world', 3, 4, 5, 'bar', 'baz']
$crazy = ['my'];
$otherItems = ['hello', '__phx_spread_5971f74e1afbd', 'world'];
$otherItems2 = ['baz'];
//$items = ['foo', ...$otherItems, ...getIds(), 'bar', ...$otherItems2];
$provider = new Provider();
// This needs recursive thinking
$items = ['foo', '__phx_spread_5971f74e1b1c6', '__phx_spread_5971f74e1b504', 'bar', '__phx_spread_5971f74e1b563', '__phx_spread_5971f74e1b5a8'];
// for (... in ...)
foreach ($items as $item) {
    echo $item, PHP_EOL;
}
//[$itemOne, ...$others, $secondLast, $last] = $items;