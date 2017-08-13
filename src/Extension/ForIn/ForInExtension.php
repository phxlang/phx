<?php

namespace Phx\Extension\ForIn;

use PhpParser\Node\Stmt\Foreach_;
use Phx\Extension\TokenExtension;
use Phx\Extension\RuleExtension;
use Phx\Yacc\Parser\Action;
use Phx\Yacc\Parser\Rule;
use Phx\Yacc\Parser\RuleGroup;
use Phx\Yacc\Parser\Token\Token;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class ForInExtension implements RuleExtension, TokenExtension
{

    const T_IN = 'T_IN';

    /**
     * @param RuleGroup[] $ruleGroups
     */
    public function modifyYaccRules(array &$ruleGroups)
    {
        foreach ($ruleGroups as $group) {
            if ($group->name === 'non_empty_statement') {
                $group->list[] = new Rule(
                    "T_FOR '(' foreach_variable ".self::T_IN." expr ')' foreach_statement",
                    new Action("$$ = \\".Foreach_::class."[$5, $3[0], ['keyVar' => null, 'byRef' => $3[1], 'stmts' => $7]];")
                );
            } elseif ($group->name === 'reserved_non_modifiers') {
                $group->list[] = new Rule(self::T_IN, null, []);
            }
        }
    }

    /**
     * @param array $tokens
     * @return void
     */
    public function modifyYaccTokens(array &$tokens)
    {
        $tokens[] = new Token([self::T_IN], []);
    }

    /**
     * @param array $tokens
     * @return mixed
     */
    public function modifyLexerTokens(array &$tokens)
    {
        $tokens['in'] = self::T_IN;
    }
}
