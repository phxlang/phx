<?php

namespace Phx\Yacc\Parser;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class RuleGroup extends AbstractNode
{
    protected $name;

    protected $list;

    /**
     * RuleGroup constructor.
     * @param $name
     * @param $list
     * @param $attributes
     */
    public function __construct($name, $list, $attributes)
    {
        parent::__construct($attributes);
        $this->name = $name;
        $this->list = $list;
    }
}
