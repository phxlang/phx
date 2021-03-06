<?php

namespace Phx\Common;

use PhpParser\NodeDumper;
use PhpParser\NodeTraverserInterface;
use PhpParser\Parser;
use PhpParser\PrettyPrinterAbstract;
use Phx\Parser\Phx;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class Transpiler
{

	/**
	 * @var Phx
	 */
	private $parser;

	/**
	 * @var NodeTraverserInterface
	 */
	private $traverser;

	/**
	 * @var PrettyPrinterAbstract
	 */
	private $printer;

	/**
	 * Transpiler constructor.
	 * @param Parser $parser
	 * @param NodeTraverserInterface $traverser
	 * @param PrettyPrinterAbstract $printer
	 */
	public function __construct(Parser $parser, NodeTraverserInterface $traverser, PrettyPrinterAbstract $printer)
	{
		$this->parser = $parser;
		$this->traverser = $traverser;
		$this->printer = $printer;
	}


	public function fromFile(string $filePath): string
	{
		$fullPath = realpath($filePath);

		return $this->fromString(file_get_contents($fullPath));
	}

	public function fromString(string $code): string
	{
        $stmts = $this->parser->parse($code);

        $dumper = new NodeDumper();
        file_put_contents('dump.ast',$dumper->dump($stmts));

		$stmts = $this->traverser->traverse($stmts);

		return $this->printer->prettyPrint($stmts);
	}
}
