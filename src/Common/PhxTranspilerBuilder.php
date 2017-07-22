<?php

namespace Phx\Common;

use PhpParser\PrettyPrinter\Standard;
use Phx\Extension\ForIn\ForInExtension;
use Phx\Extension\Spread\SpreadExtension;
use Phx\Lexer\PhxLexer;
use Phx\Parser\Phx;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class PhxTranspilerBuilder extends TranspilerBuilder
{

    private function __construct()
    {
        $this
            ->setPrinter(new Standard())
            ->setParser(new Phx(new PhxLexer()))
            ->registerExtension(new SpreadExtension())
            ->registerExtension(new ForInExtension())
        ;
    }

    /**
     * @return TranspilerBuilder
     */
    public static function create()
    {
        return new self();
    }
}