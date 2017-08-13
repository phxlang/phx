<?php

namespace Phx\Yacc\Lexer;

use PhpParser\ErrorHandler;
use PhpParser\Lexer;
use Phx\Parser\YaccTokens;

/**
 * @author Pascal <pascal@timesplinter.ch>
 */
class YaccLexer extends Lexer
{

    /**
     * YaccLexer constructor.
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        parent::__construct($options);

        // map of tokens to drop while lexing (the map is only used for isset lookup,
        // that's why the value is simply set to 1; the value is never actually used.)
        $this->dropTokens = array_fill_keys(
            [YaccTokens::T_WHITESPACE, YaccTokens::T_COMMENT], 1
        );
    }

    public function startLexing($code, ErrorHandler $errorHandler = null)
    {
        if (null === $errorHandler) {
            $errorHandler = new ErrorHandler\Throwing();
        }

        $this->code = $code; // keep the code around for __halt_compiler() handling
        $this->pos  = -1;
        $this->line =  1;
        $this->filePos = 0;

        // If inline HTML occurs without preceding code, treat it as if it had a leading newline.
        // This ensures proper composability, because having a newline is the "safe" assumption.
        $this->prevCloseTagHasNewline = true;

        $scream = ini_set('xdebug.scream', '0');

        $this->resetErrors();
        $this->tokens = $this->token_get_all($code);
        $this->handleErrors($errorHandler);

        if (false !== $scream) {
            ini_set('xdebug.scream', $scream);
        }
    }

    protected function token_get_all(string $code)
    {
        $tokenTokens = [
            '%token' => YaccTokens::T_TOKEN,
            '%left' => YaccTokens::T_LEFT,
            '%right' => YaccTokens::T_RIGHT,
            '%nonassoc' => YaccTokens::T_NONASSOC,
            '%expect' => YaccTokens::T_EXPECT,
            '%pure_parser' => YaccTokens::T_PURE_PARSER
        ];

        if (0 === preg_match_all('/((?:(?:'.implode('|', array_keys($tokenTokens)).')(?=\s))|\'.\'|%%|[|:;]|(?<=\s)[{}](?=\s)|\/\*.+?\*\/|\s+)/s', $code, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
            return [];
        }

        $tokens = [];
        $pos = 0;

        foreach ($matches as $match) {
            list($tokenValue, $offset) = $match[0];

            if ($pos !== $offset) {
                $buffer = substr($code, $pos, $offset-$pos);
                //$tokens[] = substr($code, $pos, $offset-$pos);
                $trimmedBuffer = trim($buffer);
                if (0 !== preg_match('/^\d+$/', $trimmedBuffer)) {
                    $tokens[] = [YaccTokens::T_NUM, (int) $trimmedBuffer];
                } else {
                    $tokens[] = [YaccTokens::T_STRING, $trimmedBuffer];
                }
            }

            $token = [999];

            if ($tokenValue === '%%') {
                $token = [YaccTokens::T_DOUBLE_PERCENTAGE];
            } elseif ($tokenValue === '{') {
                $token = [YaccTokens::T_CURLY_OPEN];
            } elseif ($tokenValue === '}') {
                $token = [YaccTokens::T_CURLY_CLOSE];
            } elseif ($tokenValue === ':') {
                $token = [YaccTokens::T_COLON];
            } elseif ($tokenValue === ';') {
                $token = [YaccTokens::T_SEMICOLON];
            } elseif ($tokenValue === '|') {
                $token = [YaccTokens::T_PIPE];
            } elseif (1 === preg_match('/^\s+$/', $tokenValue)) {
                $token = [YaccTokens::T_WHITESPACE];
            } elseif (1 === preg_match('/^\/\*.+?\*\/$/s', $tokenValue)) {
                $token = [YaccTokens::T_COMMENT];
            } elseif (1 === preg_match('/^\'.\'$/', $tokenValue)) {
                $token = [YaccTokens::T_STRING];
            } elseif (true === isset($tokenTokens[$tokenValue])) {
                $token = [$tokenTokens[$tokenValue]];
            }
            $token[] = $tokenValue;
            $tokens[] = $token;

            $pos = $offset + strlen($tokenValue);
        }

        $inAction = false;
        $actionStart = null;
        $tokenBuffer = '';

        $curls = 0;

        $cleanedTokens = [];

        foreach ($tokens as $token) {
            if ($token[0] === YaccTokens::T_CURLY_OPEN) {
                ++$curls;
                if ($inAction === false) {
                    $cleanedTokens[] = $token;
                    $inAction = true;
                    continue;
                }
            } elseif ($token[0] === YaccTokens::T_CURLY_CLOSE && --$curls === 0) {
                if ('' !== $tokenBuffer) {
                    $trimmedBuffer = trim(preg_replace('/\s+/', ' ', $tokenBuffer));
                    $cleanedTokens[] = [YaccTokens::T_STRING, $trimmedBuffer];
                    $tokenBuffer = '';
                }
                $inAction = false;
                $cleanedTokens[] = $token;
                continue;
            }

            if (true === $inAction) {
                $tokenBuffer .= $token[1];
                continue;
            }

            $cleanedTokens[] = $token;
        }

        $tokens = $cleanedTokens;

        $cleanedTokens = [];

        $inRule = false;

        foreach ($tokens as $token) {
            if (in_array($token[0], [YaccTokens::T_COLON])) {
                $inRule = true;
            } elseif (in_array($token[0], [YaccTokens::T_PIPE, YaccTokens::T_SEMICOLON, YaccTokens::T_CURLY_OPEN])) {
                if('' !== $tokenBuffer) {
                    $trimmedBuffer = trim(preg_replace('/\s+/', ' ', $tokenBuffer));
                    $cleanedTokens[] = [YaccTokens::T_STRING, $trimmedBuffer];
                    $tokenBuffer = '';
                }

                if ($token[0] !== YaccTokens::T_PIPE) {
                    $inRule = false;
                } else {
                    $inRule = true;
                }
            } elseif (true === $inRule) {
                $tokenBuffer .= $token[1];
                continue;
            }

            $cleanedTokens[] = $token;
        }

        $dump = '';

        foreach ($cleanedTokens as $token) {
            $dump .= $token[0] . ': ' . $token[1] . PHP_EOL;
        }

        return $cleanedTokens;
    }

    /**
     * Creates the token map.
     *
     * The token map maps the PHP internal token identifiers
     * to the identifiers used by the Parser. Additionally it
     * maps T_OPEN_TAG_WITH_ECHO to T_ECHO and T_CLOSE_TAG to ';'.
     *
     * @return array The token map
     */
    protected function createTokenMap() {
        $tokenClass = new \ReflectionClass(YaccTokens::class);

        return array_combine($tokenClass->getConstants(), $tokenClass->getConstants());
    }
}
