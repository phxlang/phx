<?php

declare(strict_types=1);

namespace Phx\Yacc;

use PhpYacc\Yacc\Lexer;
use PhpYacc\Yacc\Token as YaccToken;
use Phx\Yacc\Parser\Rule;
use Phx\Yacc\Parser\RuleSet;
use Phx\Yacc\Parser\Token\AbstractToken;
use Phx\Yacc\Parser\Token\Token;
use Phx\Yacc\Parser\Token\Left;
use Phx\Yacc\Parser\Token\NonAssoc;
use Phx\Yacc\Parser\Token\Right;

class GrammarFactory
{

    public static function fromString(string $code): Grammar
    {
        $lexer = new Lexer();
        $lexer->startLexing($code, 'YY');

        $grammar = new Grammar();

        self::getTokens($lexer, $grammar);
        self::getRuleSets($lexer, $grammar);
        self::getPrograms($lexer, $grammar);

        return $grammar;
    }

    private static function getTokens(Lexer $lexer, Grammar $grammar): void
    {
        $tokens = [];
        $currentToken = null;

        while (($t = $lexer->get())->t !== YaccToken::MARK) {
            if ($t->t === YaccToken::PURE_PARSER) {
                $grammar->pureParser = true;
            } elseif ($t->t === YaccToken::EXPECT) {
                if (($nt = $lexer->get())->t !== YaccToken::NUMBER) {
                    throw new \RuntimeException('%expect must be followed by a number (L:'.$t->ln.')');
                }
                $grammar->expect = (int) $nt->v;
            } elseif (in_array($t->t, [YaccToken::TOKEN, YaccToken::LEFT, YaccToken::RIGHT, YaccToken::NONASSOC], true)) {
                if (null !== $currentToken) {
                    $tokens[] = $currentToken;
                }
                $currentToken = self::getNewTokenByType($t->t);
            } elseif ($t->t === YaccToken::NAME) {
                if (null === $currentToken) {
                    throw new \RuntimeException('Names can only occur in token context (L:'.$t->ln.')');
                }

                $currentToken->addName($t->v);
            }
        }

        if (null !== $currentToken) {
            $tokens[] = $currentToken;
        }

        $grammar->tokens = $tokens;
    }

    private static function getRuleSets(Lexer $lexer, Grammar $grammar): void
    {
        $inRuleSet = false;
        $inRule = false;
        $ruleSet = null;
        $rule = null;

        while (($t = $lexer->get())->t !== YaccToken::MARK) {
            if ($t->t === YaccToken::NAME) {
                if (false === $inRuleSet) {
                    if ($lexer->peek()->t !== ':') {
                        throw new \RuntimeException(sprintf('Invalid token "%s" (L:%d)', $t->v, $t->ln));
                    }
                    $ruleSet = new RuleSet($t->v);
                } else {
                    while ($lexer->peek()->t !== ';') {
                        $ruleSet->addRule(self::getRule($lexer));
                        exit;
                    }
                }
            } elseif (';' === $t->t) {
                if (false === $inRuleSet) {
                    throw new \RuntimeException(sprintf('Invalid token "%s" (L:%d)', $t->v, $t->ln));
                }
                $inRuleSet = false;
            } else {
                var_dump($t, $lexer->get()); exit;
            }
        }
    }

    private static function getRule(Lexer $lexer): Rule
    {
        $rule = new Rule($lexer->get()->v);

        while (false === in_array(($t = $lexer->get())->t, ['}', '|'], true)) {
            var_dump($t);
        }

        return $rule;
    }

    private static function getPrograms(Lexer $lexer, Grammar $grammar): void
    {
        while (($t = $lexer->get())->t !== YaccToken::MARK && $t->t !== 'EOF') {

        }
    }

    private static function getNewTokenByType($type): AbstractToken
    {
        if ($type === YaccToken::NONASSOC) {
            return new NonAssoc();
        } elseif ($type === YaccToken::LEFT) {
            return new Left();
        } elseif ($type === YaccToken::RIGHT) {
            return new Right();
        } elseif ($type === YaccToken::TOKEN) {
            return new Token();
        }

        throw new \RuntimeException('Invalid token: ' . $type);
    }
}
