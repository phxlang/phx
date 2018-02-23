<?php

namespace Phx\Yacc\Parser\Token;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
abstract class AbstractToken
{

    /**
     * @var array|string[]
     */
    private $names = [];

    /**
     * @return array|string[]
     */
    public function getNames()
    {
        return $this->names;
    }

    /**
     * @param string $name
     */
    public function addName(string $name): void
    {
        $this->names[] = $name;
    }

    /**
     * @param string $name
     */
    public function removeName(string $name): void
    {
        if (false === $pos = array_search($name, $this->names, true)) {
            return;
        }

        unset($this->names[$pos]);

        $this->names = array_values($this->names);
    }
}
