<?php

namespace test;

use Phx\Parser\Yacc;
use Phx\Yacc\Lexer\YaccLexer;
use Phx\Yacc\Printer\Pretty;

require 'vendor/autoload.php';

$parser = new Yacc(new YaccLexer());

$grammar  = file_get_contents(__DIR__.'/grammar/phpN.y');
$grammar = str_replace('%tokens', file_get_contents(__DIR__.'/grammar/tokens.y'), $grammar);

file_put_contents('dump.y', $grammar);

$stmnts = $parser->parse($grammar);

//var_dump($stmnts);

$printer = new Pretty();

echo $printer->prettyPrint([$stmnts]);
