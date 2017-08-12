<?php

namespace Phx\Yacc\Lexer;

use PhpParser\ErrorHandler;
use PhpParser\Lexer;
use PhpParser\Lexer\Emulative;
use Phx\Parser\Tokens;
use Phx\Parser\Yacc;
use Phx\Parser\YaccTokens;
use Prophecy\Doubler\ClassPatch\ReflectionClassNewInstancePatch;

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
        if (0 === preg_match_all('/(\'.\'|%%|[|%:;]|(?<=\s)[{}](?=\s)|\/\*.+?\*\/|\s+)/s', $code, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
            return [];
        }

        $tokens = [];
        $tokenValues = [];
        $pos = 0;

        foreach ($matches as $match) {
            list($tokenValue, $offset) = $match[0];

            if ($pos !== $offset) {
                //$tokens[] = substr($code, $pos, $offset-$pos);
                $tokens[] = [YaccTokens::T_STRING, substr($code, $pos, $offset-$pos)];
            }


            $token = [999];

            if ($tokenValue === '%%') {
                $token = [YaccTokens::T_DOUBLE_PERCENTAGE];
            } elseif ($tokenValue === '%') {
                $token = [YaccTokens::T_PERCENTAGE];
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
            } elseif (1 === preg_match('/^\/\*.+?\*\/$/', $tokenValue)) {
                $token = [YaccTokens::T_COMMENT];
            } elseif (1 === preg_match('/^\'.\'$/', $tokenValue)) {
                $token = [YaccTokens::T_STRING];
            }
            $token[] = $tokenValue;
            $tokens[] = $token;
            $pos = $offset + strlen($tokenValue);
        }

        $inAction = false;
        $actionStart = null;
        $tokenBuffer = '';

        $cleanedTokens = [];

        foreach ($tokens as $token) {
            if ($token[0] === YaccTokens::T_CURLY_OPEN) {
                $inAction = true;
            } elseif ($token[0] === YaccTokens::T_CURLY_CLOSE) {
                if ('' !== $tokenBuffer) {
                    $cleanedTokens[] = [YaccTokens::T_STRING, trim($tokenBuffer)];
                    $tokenBuffer = '';
                }
                $inAction = false;
            } elseif (true === $inAction) {
                $tokenBuffer .= $token[1];
                continue;
            }

            $cleanedTokens[] = $token;
        }

        $tokens = $cleanedTokens;

        $cleanedTokens = [];

        $inRule = false;

        foreach ($tokens as $token) {
            if (in_array($token[0], [YaccTokens::T_COLON, YaccTokens::T_PIPE])) {
                $inRule = true;
            } elseif (in_array($token[0], [YaccTokens::T_PIPE, YaccTokens::T_SEMICOLON, YaccTokens::T_CURLY_OPEN])) {
                if ('' !== $tokenBuffer) {
                    $cleanedTokens[] = [YaccTokens::T_STRING, trim($tokenBuffer)];
                    $tokenBuffer = '';
                }
                if ($token[0] !== YaccTokens::T_PIPE) {
                    $inRule = false;
                }
            } elseif (true === $inRule) {
                $tokenBuffer .= $token[1];
                continue;
            }

            $cleanedTokens[] = $token;
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
