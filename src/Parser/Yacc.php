<?php

namespace Phx\Parser;

use PhpParser\Error;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar;
use PhpParser\Node\Stmt;

/* This is an automatically GENERATED file, which should not be manually edited.
 * Instead edit one of the following:
 *  * the grammar files grammar/php5.y or grammar/php7.y
 *  * the skeleton file grammar/parser.template
 *  * the preprocessing script grammar/rebuildParsers.php
 */
class Yacc extends \PhpParser\ParserAbstract
{
    protected $tokenToSymbolMapSize = 274;
    protected $actionTableSize = 29;
    protected $gotoTableSize = 12;

    protected $invalidSymbol = 17;
    protected $errorSymbol = 1;
    protected $defaultAction = -32766;
    protected $unexpectedTokenRule = 32767;

    protected $YY2TBLSTATE  = 13;
    protected $YYNLSTATES   = 23;

    protected $symbolToName = array(
        "EOF",
        "error",
        "T_DOUBLE_PERCENTAGE",
        "T_STRING",
        "T_NUM",
        "T_TOKEN",
        "T_LEFT",
        "T_RIGHT",
        "T_NONASSOC",
        "T_EXPECT",
        "T_PURE_PARSER",
        "T_CURLY_OPEN",
        "T_CURLY_CLOSE",
        "T_SEMICOLON",
        "T_COLON",
        "T_PIPE",
        "T_ENCAPSED_STRING"
    );

    protected $tokenToSymbol = array(
            0,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,   17,   17,   17,   17,
           17,   17,   17,   17,   17,   17,    1,    2,    3,    4,
            5,    6,    7,    8,    9,   10,   11,   12,   13,   14,
           17,   17,   15,   16
    );

    protected $action = array(
            3,    4,    5,    6,   18,   44,   45,   21,    0,   36,
           17,    7,   27,   15,   29,   20,   30,   43,    0,   46,
           13,    0,    0,    0,    0,    0,    0,    0,    2
    );

    protected $actionCheck = array(
            5,    6,    7,    8,    9,   10,    3,    3,    0,   13,
            3,   15,    2,    2,   12,    3,   12,    4,   -1,   16,
           11,   -1,   -1,   -1,   -1,   -1,   -1,   -1,   14
    );

    protected $actionBase = array(
           -5,   -5,    7,    3,    3,    3,    3,    7,    3,    3,
            3,    3,   10,    4,   -4,   12,   11,    9,   13,    8,
           14,    2,    0,    0,    0,    9,    0,    0,    0,    0,
            9,    0,    0,    0,    0,   12
    );

    protected $actionDefault = array(
            2,    3,32767,32767,32767,32767,32767,32767,   16,   17,
           18,   19,32767,32767,32767,32767,32767,   10,32767,32767,
        32767,32767,    5
    );

    protected $goto = array(
           48,   48,   48,   48,   50,    9,   10,   11,   37,   35,
            0,   31
    );

    protected $gotoCheck = array(
           13,   13,   13,   13,   11,   12,   12,   12,   10,    8,
           -1,    7
    );

    protected $gotoBase = array(
            0,    0,    0,    0,    0,    0,    0,   -6,    2,    0,
           -7,    3,    1,   -8
    );

    protected $gotoDefault = array(
        -32768,   19,   16,   22,   24,    1,   12,   32,   34,   14,
           38,   49,    8,   47
    );

    protected $ruleToNonTerminal = array(
            0,    1,    2,    2,    3,    4,    7,    7,    8,    8,
            8,    9,    9,   10,    6,    6,   11,   11,   11,   11,
           11,   11,   13,   13,   12,   12,    5,    5
    );

    protected $ruleToLength = array(
            1,    3,    0,    1,    3,    0,    3,    2,    2,    1,
            1,    1,    3,    4,    1,    2,    2,    2,    2,    2,
            2,    1,    1,    1,    1,    2,    1,    2
    );

    protected $productions = array(
        "start : start",
        "start : optional_section_tokens section_rules optional_section_programs",
        "optional_section_tokens : /* empty */",
        "optional_section_tokens : token_list",
        "section_rules : T_DOUBLE_PERCENTAGE rule_group_list T_DOUBLE_PERCENTAGE",
        "optional_section_programs : /* empty */",
        "action : T_CURLY_OPEN T_STRING T_CURLY_CLOSE",
        "action : T_CURLY_OPEN T_CURLY_CLOSE",
        "rule : T_STRING action",
        "rule : action",
        "rule : T_STRING",
        "rule_list : rule",
        "rule_list : rule_list T_PIPE rule",
        "rule_group : T_STRING T_COLON rule_list T_SEMICOLON",
        "rule_group_list : rule_group",
        "rule_group_list : rule_group_list rule_group",
        "token : T_TOKEN tokens",
        "token : T_LEFT tokens",
        "token : T_RIGHT tokens",
        "token : T_NONASSOC tokens",
        "token : T_EXPECT T_NUM",
        "token : T_PURE_PARSER",
        "token_name : T_STRING",
        "token_name : T_ENCAPSED_STRING",
        "tokens : token_name",
        "tokens : tokens token_name",
        "token_list : token",
        "token_list : token_list token"
    );

    protected function reduceRule0() {
        $this->semValue = $this->semStack[$this->stackPos];
    }

    protected function reduceRule1() {
         $this->semValue = new \Phx\Yacc\Parser\Definition($this->semStack[$this->stackPos-(3-1)], $this->semStack[$this->stackPos-(3-2)], $this->semStack[$this->stackPos-(3-3)], $this->startAttributeStack[$this->stackPos-(3-1)] + $this->endAttributes);
    }

    protected function reduceRule2() {
         $this->semValue = array();
    }

    protected function reduceRule3() {
         $this->semValue = $this->semStack[$this->stackPos-(1-1)];
    }

    protected function reduceRule4() {
         $this->semValue = $this->semStack[$this->stackPos-(3-2)];
    }

    protected function reduceRule5() {
         $this->semValue = array();
    }

    protected function reduceRule6() {
         $this->semValue = new \Phx\Yacc\Parser\Action($this->semStack[$this->stackPos-(3-2)], $this->startAttributeStack[$this->stackPos-(3-1)] + $this->endAttributes);
    }

    protected function reduceRule7() {
         $this->semValue = new \Phx\Yacc\Parser\Action($this->startAttributeStack[$this->stackPos-(2-1)] + $this->endAttributes);
    }

    protected function reduceRule8() {
         $this->semValue = new \Phx\Yacc\Parser\Rule($this->semStack[$this->stackPos-(2-1)], $this->semStack[$this->stackPos-(2-2)], $this->startAttributeStack[$this->stackPos-(2-1)] + $this->endAttributes);
    }

    protected function reduceRule9() {
         $this->semValue = new \Phx\Yacc\Parser\Rule(null, $this->semStack[$this->stackPos-(1-1)], $this->startAttributeStack[$this->stackPos-(1-1)] + $this->endAttributes);
    }

    protected function reduceRule10() {
         $this->semValue = new \Phx\Yacc\Parser\Rule($this->semStack[$this->stackPos-(1-1)], null, $this->startAttributeStack[$this->stackPos-(1-1)] + $this->endAttributes);
    }

    protected function reduceRule11() {
         $this->semValue = array($this->semStack[$this->stackPos-(1-1)]);
    }

    protected function reduceRule12() {
         $this->semStack[$this->stackPos-(3-1)][] = $this->semStack[$this->stackPos-(3-3)]; $this->semValue = $this->semStack[$this->stackPos-(3-1)];
    }

    protected function reduceRule13() {
         $this->semValue = new \Phx\Yacc\Parser\RuleGroup($this->semStack[$this->stackPos-(4-1)], $this->semStack[$this->stackPos-(4-3)], $this->startAttributeStack[$this->stackPos-(4-1)] + $this->endAttributes);
    }

    protected function reduceRule14() {
         $this->semValue = array($this->semStack[$this->stackPos-(1-1)]);
    }

    protected function reduceRule15() {
         $this->semStack[$this->stackPos-(2-1)][] = $this->semStack[$this->stackPos-(2-2)]; $this->semValue = $this->semStack[$this->stackPos-(2-1)];
    }

    protected function reduceRule16() {
         $this->semValue = new \Phx\Yacc\Parser\Token($this->semStack[$this->stackPos-(2-2)], $this->startAttributeStack[$this->stackPos-(2-1)] + $this->endAttributes);
    }

    protected function reduceRule17() {
         $this->semValue = new \Phx\Yacc\Parser\Token($this->semStack[$this->stackPos-(2-2)], $this->startAttributeStack[$this->stackPos-(2-1)] + $this->endAttributes);
    }

    protected function reduceRule18() {
         $this->semValue = new \Phx\Yacc\Parser\Token($this->semStack[$this->stackPos-(2-2)], $this->startAttributeStack[$this->stackPos-(2-1)] + $this->endAttributes);
    }

    protected function reduceRule19() {
         $this->semValue = new \Phx\Yacc\Parser\Token($this->semStack[$this->stackPos-(2-2)], $this->startAttributeStack[$this->stackPos-(2-1)] + $this->endAttributes);
    }

    protected function reduceRule20() {
         $this->semValue = new \Phx\Yacc\Parser\Expect($this->semStack[$this->stackPos-(2-2)], $this->startAttributeStack[$this->stackPos-(2-1)] + $this->endAttributes);
    }

    protected function reduceRule21() {
         $this->semValue = new \Phx\Yacc\Parser\PureParser($this->startAttributeStack[$this->stackPos-(1-1)] + $this->endAttributes);
    }

    protected function reduceRule22() {
         $this->semValue = $this->semStack[$this->stackPos-(1-1)];
    }

    protected function reduceRule23() {
         $this->semValue = $this->semStack[$this->stackPos-(1-1)];
    }

    protected function reduceRule24() {
         $this->semValue = array($this->semStack[$this->stackPos-(1-1)]);
    }

    protected function reduceRule25() {
         $this->semStack[$this->stackPos-(2-1)][] = $this->semStack[$this->stackPos-(2-2)]; $this->semValue = $this->semStack[$this->stackPos-(2-1)];
    }

    protected function reduceRule26() {
         $this->semValue = array($this->semStack[$this->stackPos-(1-1)]);
    }

    protected function reduceRule27() {
         $this->semStack[$this->stackPos-(2-1)][] = $this->semStack[$this->stackPos-(2-2)]; $this->semValue = $this->semStack[$this->stackPos-(2-1)];
    }
}
