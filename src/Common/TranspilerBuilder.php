<?php

namespace Phx\Common;

use PhpParser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\PrettyPrinterAbstract;
use Phx\Extension\Extension;
use Phx\Extension\VisitorExtension;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class TranspilerBuilder
{
	/**
	 * @var Extension[]
	 */
	private $extensions = [];

	/**
	 * @var Parser
	 */
	private $parser;

	/**
	 * @var PrettyPrinterAbstract
	 */
	private $printer;

	/**
	 * TranspilerBuilder constructor.
	 */
	private function __construct()
	{
	}

	/**
	 * @return TranspilerBuilder
	 */
	public static function create()
	{
		return new self();
	}

    /**
     * @return Transpiler
     * @throws TranspilerBuilderException
     */
	public function build(): Transpiler
	{
	    if (null === $this->parser) {
            throw new TranspilerBuilderException('No parser set');
        } elseif (null === $this->printer) {
	        throw new TranspilerBuilderException('No printer set');
        }

		$traverser = new NodeTraverser();

		foreach ($this->extensions as $extension) {
			if ($extension instanceof VisitorExtension) {
				foreach ($extension->getVisitors() as $visitor) {
					$traverser->addVisitor($visitor);
				}
			}
		}

		return new Transpiler($this->parser, $traverser, $this->printer);
	}

	/**
	 * @param Extension $extension
	 * @return $this
	 */
	public function registerExtension(Extension $extension)
	{
	    if (true === $extension->supports(PHP_VERSION_ID)) {
            $this->extensions[] = $extension;;
        }

		return $this;
	}

	/**
	 * @param Parser $parser
	 * @return self
	 */
	public function setParser(Parser $parser): self
	{
		$this->parser = $parser;
		return $this;
	}

	/**
	 * @param PrettyPrinterAbstract $printer
	 * @return TranspilerBuilder
	 */
	public function setPrinter(PrettyPrinterAbstract $printer): self
	{
		$this->printer = $printer;
		return $this;
	}
}
