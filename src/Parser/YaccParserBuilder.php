<?php

namespace Phx\Parser;

use Phx\Extension\Extension;
use Phx\Extension\TokenExtension;
use Phx\Parser\Node\Expr\UnpackArrayItem;

class YaccParserBuilder
{

	const LIB = '(?(DEFINE)
	    (?<singleQuotedString>\'[^\\\\\']*+(?:\\\\.[^\\\\\']*+)*+\')
	    (?<doubleQuotedString>"[^\\\\"]*+(?:\\\\.[^\\\\"]*+)*+")
	    (?<string>(?&singleQuotedString)|(?&doubleQuotedString))
	    (?<comment>/\*[^*]*+(?:\*(?!/)[^*]*+)*+\*/)
	    (?<code>\{[^\'"/{}]*+(?:(?:(?&string)|(?&comment)|(?&code)|/)[^\'"/{}]*+)*+})
	)';

	const PARAMS = '\[(?<params>[^[\]]*+(?:\[(?&params)\][^[\]]*+)*+)\]';
	const ARGS   = '\((?<args>[^()]*+(?:\((?&args)\)[^()]*+)*+)\)';

	private $extensions = [];

	public function addExtension(Extension $extension)
    {
        $this->extensions[] = $extension;
    }

	public function build(array $options = [])
	{
		$grammarDir = __DIR__ . '/../../grammar';
		$phpParserVendorDir = __DIR__ . '/../../vendor/nikic/php-parser';
		$resultDir = __DIR__;

		$grammarFileToName = [
			$grammarDir . '/yacc.y' => 'Yacc',
		];

		$tokensFile     = $grammarDir . '/yacc_tokens.y';
		$skeletonFile   = $phpParserVendorDir . '/grammar/parser.template';
		$tokensTemplate = $phpParserVendorDir . '/grammar/tokens.template';
		$tmpIdentifier  = uniqid();
		$tmpGrammarFile = sys_get_temp_dir().'/'.$tmpIdentifier.'_tmp_parser.phpy';
		$tmpResultFile  = sys_get_temp_dir().'/'.$tmpIdentifier.'_tmp_parser.php';
		$tokensResultsFile = $resultDir . '/YaccTokens.php';

		// check for kmyacc.exe binary in this directory, otherwise fall back to global name
		$kmyacc = __DIR__ . '/kmyacc.exe';
		if (!file_exists($kmyacc)) {
			$kmyacc = 'kmyacc';
		}

		$options = array_flip($options);
		$optionDebug = isset($options['--debug']);
		$optionKeepTmpGrammar = isset($options['--keep-tmp-grammar']);

		///////////////////
		/// Main script ///
		///////////////////

		$tokens = file_get_contents($tokensFile);

		foreach ($grammarFileToName as $grammarFile => $name) {
			echo "Building temporary $name grammar file.\n";

			$grammarCode = file_get_contents($grammarFile);
			$grammarCode = str_replace('%tokens', $tokens, $grammarCode);

			$grammarCode = $this->resolveNodes($grammarCode);
			$grammarCode = $this->resolveMacros($grammarCode);
			$grammarCode = $this->resolveStackAccess($grammarCode);

			file_put_contents($tmpGrammarFile, $grammarCode);

			file_put_contents('dump.y', $grammarCode);

			$additionalArgs = $optionDebug ? '-t -v' : '';

			echo "Building $name parser.\n";
			//echo "$kmyacc $additionalArgs -l -m $skeletonFile -p $name $tmpGrammarFile 2>&1"; exit;
			$output = trim(shell_exec("$kmyacc $additionalArgs -l -m $skeletonFile -p $name $tmpGrammarFile 2>&1"));
			echo "Output: \"$output\"\n";

			$resultCode = file_get_contents($tmpResultFile);
			$resultCode = strtr($resultCode, ['namespace PhpParser\Parser;' => 'namespace '.__NAMESPACE__.';']);
			$resultCode = $this->removeTrailingWhitespace($resultCode);

			$this->ensureDirExists($resultDir);
			file_put_contents("$resultDir/$name.php", $resultCode);
			unlink($tmpResultFile);

			echo "Building token definition.\n";
			$output = trim(shell_exec("$kmyacc -l -m $tokensTemplate $tmpGrammarFile 2>&1"));
			assert($output === '');
			$resultCode = file_get_contents($tmpResultFile);
			$resultCode = strtr($resultCode, ['namespace PhpParser\Parser;' => 'namespace '.__NAMESPACE__.';', 'class Tokens' => 'class YaccTokens']);
			file_put_contents($tokensResultsFile, $resultCode);

			if (!$optionKeepTmpGrammar) {
				unlink($tmpGrammarFile);
			}
		}
	}

	///////////////////////////////
	/// Preprocessing functions ///
	///////////////////////////////

	protected function resolveNodes($code) {
		return preg_replace_callback(
			'~(?<![\w])(?<name>\\\\?[A-Z][a-zA-Z_\\\\]++)\s*' . self::PARAMS . '~',
			function($matches) {
				// recurse
				$matches['params'] = $this->resolveNodes($matches['params']);

				$params = $this->magicSplit(
					'(?:' . self::PARAMS . '|' . self::ARGS . ')(*SKIP)(*FAIL)|,',
					$matches['params']
				);

				$paramCode = '';
				foreach ($params as $param) {
					$paramCode .= $param . ', ';
				}
				return 'new ' . $matches['name'] . '(' . $paramCode . 'attributes())';
			},
			$code
		);
	}

	protected function resolveMacros($code) {
		return preg_replace_callback(
			'~\b(?<!::|->)(?!array\()(?<name>[a-z][A-Za-z]++)' . self::ARGS . '~',
			function($matches) {
				// recurse
				$matches['args'] = $this->resolveMacros($matches['args']);

				$name = $matches['name'];
				$args = $this->magicSplit(
					'(?:' . self::PARAMS . '|' . self::ARGS . ')(*SKIP)(*FAIL)|,',
					$matches['args']
				);

				if ('attributes' == $name) {
					$this->assertArgs(0, $args, $name);
					return '$this->startAttributeStack[#1] + $this->endAttributes';
				}

				if ('stackAttributes' == $name) {
					$this->assertArgs(1, $args, $name);
					return '$this->startAttributeStack[' . $args[0] . ']'
						. ' + $this->endAttributeStack[' . $args[0] . ']';
				}

				if ('init' == $name) {
					return '$$ = array(' . implode(', ', $args) . ')';
				}

				if ('push' == $name) {
					$this->assertArgs(2, $args, $name);

					return $args[0] . '[] = ' . $args[1] . '; $$ = ' . $args[0];
				}

				if ('pushNormalizing' == $name) {
					$this->assertArgs(2, $args, $name);

					return 'if (is_array(' . $args[1] . ')) { $$ = array_merge(' . $args[0] . ', ' . $args[1] . '); }'
						. ' else { ' . $args[0] . '[] = ' . $args[1] . '; $$ = ' . $args[0] . '; }';
				}

				if ('toArray' == $name) {
					$this->assertArgs(1, $args, $name);

					return 'is_array(' . $args[0] . ') ? ' . $args[0] . ' : array(' . $args[0] . ')';
				}

				if ('parseVar' == $name) {
					$this->assertArgs(1, $args, $name);

					return 'substr(' . $args[0] . ', 1)';
				}

				if ('parseEncapsed' == $name) {
					$this->assertArgs(3, $args, $name);

					return 'foreach (' . $args[0] . ' as $s) { if ($s instanceof Node\Scalar\EncapsedStringPart) {'
						. ' $s->value = Node\Scalar\String_::parseEscapeSequences($s->value, ' . $args[1] . ', ' . $args[2] . '); } }';
				}

				if ('parseEncapsedDoc' == $name) {
					$this->assertArgs(2, $args, $name);

					return 'foreach (' . $args[0] . ' as $s) { if ($s instanceof Node\Scalar\EncapsedStringPart) {'
						. ' $s->value = Node\Scalar\String_::parseEscapeSequences($s->value, null, ' . $args[1] . '); } }'
						. ' $s->value = preg_replace(\'~(\r\n|\n|\r)\z~\', \'\', $s->value);'
						. ' if (\'\' === $s->value) array_pop(' . $args[0] . ');';
				}

				if ('makeNop' == $name) {
					$this->assertArgs(2, $args, $name);

					return '$startAttributes = ' . $args[1] . ';'
						. ' if (isset($startAttributes[\'comments\']))'
						. ' { ' . $args[0] . ' = new Stmt\Nop([\'comments\' => $startAttributes[\'comments\']]); }'
						. ' else { ' . $args[0] . ' = null; }';
				}

				if ('strKind' == $name) {
					$this->assertArgs(1, $args, $name);

					return '(' . $args[0] . '[0] === "\'" || (' . $args[0] . '[1] === "\'" && '
						. '(' . $args[0] . '[0] === \'b\' || ' . $args[0] . '[0] === \'B\')) '
						. '? Scalar\String_::KIND_SINGLE_QUOTED : Scalar\String_::KIND_DOUBLE_QUOTED)';
				}

				if ('setDocStringAttrs' == $name) {
					$this->assertArgs(2, $args, $name);

					return $args[0] . '[\'kind\'] = strpos(' . $args[1] . ', "\'") === false '
						. '? Scalar\String_::KIND_HEREDOC : Scalar\String_::KIND_NOWDOC; '
						. 'preg_match(\'/\A[bB]?<<<[ \t]*[\\\'"]?([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)[\\\'"]?(?:\r\n|\n|\r)\z/\', ' . $args[1] . ', $matches); '
						. $args[0] . '[\'docLabel\'] = $matches[1];';
				}

				if ('prependLeadingComments' == $name) {
					$this->assertArgs(1, $args, $name);

					return '$attrs = $this->startAttributeStack[#1]; $stmts = ' . $args[0] . '; '
						. 'if (!empty($attrs[\'comments\']) && isset($stmts[0])) {'
						. '$stmts[0]->setAttribute(\'comments\', '
						. 'array_merge($attrs[\'comments\'], $stmts[0]->getAttribute(\'comments\', []))); }';
				}

				return $matches[0];
			},
			$code
		);
	}

	protected function assertArgs($num, $args, $name) {
		if ($num != count($args)) {
			die('Wrong argument count for ' . $name . '().');
		}
	}

	protected function resolveStackAccess($code) {
		$code = preg_replace('/\$\d+/', '$this->semStack[$0]', $code);
		$code = preg_replace('/#(\d+)/', '$$1', $code);
		return $code;
	}

	protected function removeTrailingWhitespace($code) {
		$lines = explode("\n", $code);
		$lines = array_map('rtrim', $lines);
		return implode("\n", $lines);
	}

	protected function ensureDirExists($dir) {
		if (!is_dir($dir)) {
			mkdir($dir, 0777, true);
		}
	}

	//////////////////////////////
	/// Regex helper functions ///
	//////////////////////////////

	protected function regex($regex) {
		return '~' . self::LIB . '(?:' . str_replace('~', '\~', $regex) . ')~';
	}

	protected function magicSplit($regex, $string) {
		$pieces = preg_split($this->regex('(?:(?&string)|(?&comment)|(?&code))(*SKIP)(*FAIL)|' . $regex), $string);

		foreach ($pieces as &$piece) {
			$piece = trim($piece);
		}

		if ($pieces === ['']) {
			return [];
		}

		return $pieces;
	}
}
