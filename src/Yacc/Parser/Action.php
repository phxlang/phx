<?php

namespace Phx\Yacc\Parser;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class Rule extends AbstractNode
{

    /**
     * @var string
     */
    public $rule;

    /**
     * @var string|null
     */
    public $action;

    /**
     * Rule constructor.
     * @param array $ruleParts
     * @param $action
     * @param $attributes
     */
    public function __construct(array $ruleParts, $action, $attributes)
    {
        parent::__construct($attributes);
        $this->rule = $rule;
        $this->action = $action;
    }
}
