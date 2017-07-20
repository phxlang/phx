<?php

namespace Phx\Lexer;

use PhpParser\ErrorHandler;
use PhpParser\Lexer\Emulative;
use Phx\Parser\Tokens;

/**
 * @author Pascal <pascal@timesplinter.ch>
 */
class PhxLexer extends Emulative
{

	const PHX_KEYWORDS = [
		'in' => Tokens::T_IN,
	];

	public function __construct(array $options = array()) {
		parent::__construct($options);

		$this->newKeywords += self::PHX_KEYWORDS;
	}

	public function startLexing($code, ErrorHandler $errorHandler = null)
	{
		parent::startLexing(strtr($code, ['<?phx' => '<?php']), $errorHandler);
	}

	protected function requiresEmulation($code) {
		if (preg_match('(<\?phx|'.implode('|',array_keys(self::PHX_KEYWORDS)).')', $code)) {
			return true;
		}

		return parent::requiresEmulation($code);
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
		$tokenMap = [];

		// 256 is the minimum possible token number, as everything below
		// it is an ASCII value
		for ($i = 256; $i < 1000; ++$i) {
			if (T_DOUBLE_COLON === $i) {
				// T_DOUBLE_COLON is equivalent to T_PAAMAYIM_NEKUDOTAYIM
				$tokenMap[$i] = Tokens::T_PAAMAYIM_NEKUDOTAYIM;
			} elseif(T_OPEN_TAG_WITH_ECHO === $i) {
				// T_OPEN_TAG_WITH_ECHO with dropped T_OPEN_TAG results in T_ECHO
				$tokenMap[$i] = Tokens::T_ECHO;
			} elseif(T_CLOSE_TAG === $i) {
				// T_CLOSE_TAG is equivalent to ';'
				$tokenMap[$i] = ord(';');
			} elseif ('UNKNOWN' !== $name = token_name($i)) {
				if ('T_HASHBANG' === $name) {
					// HHVM uses a special token for #! hashbang lines
					$tokenMap[$i] = Tokens::T_INLINE_HTML;
				} else if (defined($name = Tokens::class . '::' . $name)) {
					// Other tokens can be mapped directly
					$tokenMap[$i] = constant($name);
				}
			}
		}

		// HHVM uses a special token for numbers that overflow to double
		if (defined('T_ONUMBER')) {
			$tokenMap[T_ONUMBER] = Tokens::T_DNUMBER;
		}
		// HHVM also has a separate token for the __COMPILER_HALT_OFFSET__ constant
		if (defined('T_COMPILER_HALT_OFFSET')) {
			$tokenMap[T_COMPILER_HALT_OFFSET] = Tokens::T_STRING;
		}

		return $tokenMap;
	}
}
