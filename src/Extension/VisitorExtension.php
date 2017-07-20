<?php

namespace Phx\Extension;

use PhpParser\NodeVisitor;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
interface VisitorExtension extends Extension
{
	/**
	 * @return NodeVisitor[]
	 */
	public function getVisitors(): array;
}
