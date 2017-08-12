<?php


namespace Phx\Yacc\Parser;


/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class Rule extends AbstractNode
{
    protected $rule;

    protected $action;

    /**
     * Rule constructor.
     * @param $rule
     * @param $action
     * @param $attributes
     */
    public function __construct($rule, $action, $attributes)
    {
        parent::__construct($attributes);
        $this->rule = $rule;
        $this->action = $action;
    }
}
