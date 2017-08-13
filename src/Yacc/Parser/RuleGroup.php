<?php

namespace Phx\Yacc\Parser;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class RuleGroup extends AbstractNode
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var array|Rule[]
     */
    public $list;

    /**
     * RuleGroup constructor.
     * @param string $name
     * @param array $list
     * @param array $attributes
     */
    public function __construct(string $name, array $list, array $attributes)
    {
        parent::__construct($attributes);
        $this->name = $name;
        $this->list = $list;
    }
}
