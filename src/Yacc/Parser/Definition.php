<?php


namespace Phx\Yacc\Parser;


/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class Definition extends AbstractNode
{

    public $tokens;

    public $rules;

    public $programs;

    public function __construct($tokens, $rules, $programs, $attributes)
    {
        parent::__construct($attributes);
        $this->tokens = $tokens;
        $this->rules = $rules;
        $this->programs = $programs;
    }
}
