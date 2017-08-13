<?php

namespace Phx\Yacc\Parser;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class Expect extends AbstractNode
{

    /**
     * @var int
     */
    public $conflicts;

    /**
     * Token constructor.
     * @param int $conflicts
     * @param array $attributes
     */
    public function __construct(int $conflicts, array $attributes)
    {
        parent::__construct($attributes);
        $this->conflicts = $conflicts;
    }
}
