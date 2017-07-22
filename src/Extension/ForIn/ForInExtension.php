<?php

namespace Phx\Extension\ForIn;
use PhpParser\Node\Stmt\Foreach_;
use Phx\Extension\TokenExtension;
use Phx\Extension\YaccExtension;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class ForInExtension implements TokenExtension, YaccExtension
{

    /**
     * @return array
     */
    public function extendTokens(): array
    {
        return [
            'in' => [TokenExtension::TYPE_TOKEN, 'T_IN']
        ];
    }

    /**
     * @return array
     */
    public function extendYacc(): array
    {
        return [
            'non_empty_statement' => [
                "T_FOR '(' foreach_variable T_IN expr ')' foreach_statement" =>
                    "{ $$ = ".Foreach_::class."[$5, $3[0], ['keyVar' => null, 'byRef' => $3[1], 'stmts' => $7]]; }"
            ]
        ];
    }
}