<?php

namespace Phx\Common;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class NodeConnector extends NodeVisitorAbstract
{

	const ATTR_PARENT = 'parent';

	const ATTR_PREV = 'prev';

	const ATTR_NEXT = 'next';

	/** @var array */
	private $stack;

	/** @var Node */
	private $prev;

	public function beforeTraverse(array $nodes) {
		$this->stack = [];
		$this->prev = null;
	}
	public function enterNode(Node $node) {
		if (!empty($this->stack)) {
			$node->setAttribute(self::ATTR_PARENT, $this->stack[count($this->stack)-1]);
		}
		if ($this->prev && $this->prev->getAttribute(self::ATTR_PARENT) == $node->getAttribute(self::ATTR_PARENT)) {
			$node->setAttribute(self::ATTR_PREV, $this->prev);
			$this->prev->setAttribute(self::ATTR_NEXT, $node);
		}
		$this->stack[] = $node;
	}
	public function leaveNode(Node $node) {
		$this->prev = $node;
		array_pop($this->stack);
	}
}
