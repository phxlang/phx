<?php

declare(strict_types=1);

namespace Phx\Yacc;

class Grammar
{

    /**
     * @var null|bool
     */
    public $pureParser;

    /**
     * @var null|int
     */
    public $expect;

    /**
     * @var array|Token[]
     */
    public $tokens = [];

    /**
     * @var array
     */
    public $rules = [];

    /**
     * @var array
     */
    public $programs = [];
}
