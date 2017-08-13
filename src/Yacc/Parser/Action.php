<?php

namespace Phx\Yacc\Parser;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class Action extends AbstractNode
{

    /**
     * @var string|null
     */
    public $action;

    /**
     * Rule constructor.
     * @param string|null $action
     * @param array $attributes
     */
    public function __construct($action, array $attributes = [])
    {
        parent::__construct($attributes);
        $this->action = $action;
    }
}
