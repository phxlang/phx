<?php

namespace Phx\Yacc\Parser;

use Phx\Yacc\Parser\Token\AbstractToken;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class Definition extends AbstractNode
{

    /**
     * @var AbstractToken[]
     */
    public $tokens;

    /**
     * @var RuleGroup[]
     */
    public $rules;

    public $programs;

    public function __construct(array $tokens, array $rules, array $programs, array $attributes)
    {
        parent::__construct($attributes);
        $this->tokens = $tokens;
        $this->rules = $rules;
        $this->programs = $programs;
    }
}
