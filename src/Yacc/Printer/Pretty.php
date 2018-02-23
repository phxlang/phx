<?php

namespace Phx\Yacc\Printer;

use Phx\Yacc\Parser\AbstractNode;
use Phx\Yacc\Parser\Definition;
use Phx\Yacc\Parser\PureParser;
use Phx\Yacc\Parser\Rule;
use Phx\Yacc\Parser\RuleSet;
use Phx\Yacc\Parser\Token\AbstractToken;
use Phx\Yacc\Parser\Expect;
use Phx\Yacc\Parser\Token\Left;
use Phx\Yacc\Parser\Token\NonAssoc;
use Phx\Yacc\Parser\Token\Right;
use Phx\Yacc\Parser\Token\Token;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class Pretty
{

    /**
     * @param array $stmts
     * @return string
     */
    public function prettyPrint(array $stmts): string
    {
        $str = '';

        foreach ($stmts as $stmt) {
            $str .= $this->prettyPrintNode($stmt);
        }

        return $str;
    }

    /**
     * @param AbstractNode $node
     * @return string
     */
    public function prettyPrintNode(AbstractNode $node): string
    {
        if ($node instanceof AbstractToken) {
            $tokenType = null;

            if ($node instanceof Token) {
                $tokenType = 'token';
            } elseif ($node instanceof NonAssoc) {
                $tokenType = 'nonassoc';
            } elseif ($node instanceof Left) {
                $tokenType = 'left';
            } elseif ($node instanceof Right) {
                $tokenType = 'right';
            }

            return '%' . $tokenType . ' ' . implode(' ', $node->names) . PHP_EOL;
        } elseif ($node instanceof Expect) {
            return '%expect ' . $node->conflicts . PHP_EOL;
        } elseif ($node instanceof PureParser) {
            return '%pure_parser' . PHP_EOL;
        } elseif ($node instanceof Definition) {
            $definitionStr = $this->prettyPrint($node->tokens) . PHP_EOL;
            $definitionStr .= '%%' . PHP_EOL . $this->prettyPrint($node->rules) . '%%' . PHP_EOL;
            $definitionStr .= $this->prettyPrint($node->programs);

            return $definitionStr;
        } elseif ($node instanceof RuleSet) {
            return $this->printRuleGroup($node) . PHP_EOL . PHP_EOL;
        } else {
            return 'UNKNOWN: ' . get_class($node) . PHP_EOL;
        }
    }

    protected function printRuleGroup(RuleSet $ruleGroup): string
    {
        $str = $ruleGroup->name . ':' . PHP_EOL;

        $i = 0;
        foreach ($ruleGroup->rules as $rule) {
            $preRule = $i === 0 ? '      ' : '    | ';
            $str .= $preRule . $this->printRule($rule) . PHP_EOL;
            ++$i;
        }

        $str .= ';';

        return $str;
    }

    protected function printRule(Rule $rule): string
    {
        $ruleStr = ($rule->ruleParts === '') ? '/* empty */' : $rule->ruleParts;
        $actionStr = ($rule->action === null)
            ? null
            : '{ ' . (trim($rule->action->action) !== '' ? $rule->action->action : '/* empty */') . ' }';

        if($actionStr === null) {
            return $ruleStr;
        } else {
            $singleLineRule = str_pad($ruleStr, 60, ' ', STR_PAD_RIGHT) . ' ' . $actionStr;
            if (strlen($ruleStr) > 60 || strlen($singleLineRule) > 120) {
                return $ruleStr . PHP_EOL . str_repeat(' ', 12) . $actionStr;
            }
            return $singleLineRule;
        }
    }
}