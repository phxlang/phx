<?php

namespace Phx\Yacc\Parser;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class RuleSet
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array|Rule[]
     */
    private $rules = [];

    /**
     * RuleGroup constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function addRule(Rule $rule): void
    {
        $this->rules[] = $rule;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array|Rule[]
     */
    public function getRules()
    {
        return $this->rules;
    }
}
