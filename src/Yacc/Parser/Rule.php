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
    public $ruleParts;

    /**
     * @var Action|null
     */
    public $action;

    /**
     * Rule constructor.
     * @param string|null $ruleParts
     * @param Action|null $action
     * @param array $attributes
     */
    public function __construct($ruleParts, Action $action = null, array $attributes = [])
    {
        parent::__construct($attributes);
        $this->ruleParts = $ruleParts;
        $this->action = $action;
    }
}
