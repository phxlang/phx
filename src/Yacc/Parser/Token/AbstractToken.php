<?php

namespace Phx\Yacc\Parser\Token;

use Phx\Yacc\Parser\AbstractNode;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
abstract class AbstractToken extends AbstractNode
{

    /**
     * @var string[]
     */
    public $names;

    /**
     * Token constructor.
     * @param array $names
     * @param array $attributes
     */
    public function __construct(array $names, array $attributes)
    {
        parent::__construct($attributes);
        $this->names = $names;
    }
}
