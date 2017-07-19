<?php

namespace phx\Lexer;

use PhpParser\ErrorHandler;
use PhpParser\Lexer\Emulative;
use phx\Parser\Tokens;

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
}
