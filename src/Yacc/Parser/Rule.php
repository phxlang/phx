<?php

namespace Phx\Yacc\Parser;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class Rule
{

    /**
     * @var string
     */
    private $rulePart;

    /**
     * @var Action|null
     */
    private $action;

    /**
     * @param string $rulePart
     */
    public function __construct(string $rulePart)
    {
        $this->rulePart = $rulePart;
    }

    /**
     * @param Action $action
     */
    public function setAction(Action $action): void
    {
        $this->action = $action;
    }

    /**
     * @return string
     */
    public function getRulePart(): string
    {
        return $this->rulePart;
    }

    /**
     * @return null|Action
     */
    public function getAction()
    {
        return $this->action;
    }
}
