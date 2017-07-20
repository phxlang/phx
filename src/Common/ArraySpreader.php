<?php

namespace Phx\Common;

use PhpParser\BuilderFactory;
use PhpParser\Node;
use PhpParser\NodeDumper;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use Phx\Parser\Node\Expr\UnpackArrayItem;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class ArraySpreader extends NodeVisitorAbstract
{
	/** @var UnpackArrayItem|null */
	private $currentUnpackNode = null;

	/** @var Node[] */
	private $items = [];

	public function enterNode(Node $node)
	{
		// Take all further ArrayItems out of the array,
		// Append new statement for array += spreaded variable,
		// Append new array node with rest of ArrayItems
		if (true === $node instanceof UnpackArrayItem) {
			$this->currentUnpackNode = $node;
		}
	}

	public function leaveNode(Node $node) {
		if (null === $this->currentUnpackNode) {
			return null;
		}

		if ($node === $this->currentUnpackNode) {
			return NodeTraverser::REMOVE_NODE;
		}

		if ($node->getAttribute(NodeConnector::ATTR_PARENT) === $this->currentUnpackNode->getAttribute(NodeConnector::ATTR_PARENT)) {
			$this->items[] = $node;
			return NodeTraverser::REMOVE_NODE;
		} elseif ($node === $this->currentUnpackNode->getAttribute(NodeConnector::ATTR_PARENT)->getAttribute(NodeConnector::ATTR_PARENT)) {
			/** @var Node $parent */
			$parent = $this->currentUnpackNode->getAttribute(NodeConnector::ATTR_PARENT);
			/** @var Node\Expr\Variable $node3 */
			$node3 = $parent->getAttribute(NodeConnector::ATTR_PREV);

			$plusSpreadArrayNode = new Node\Expr\AssignOp\Plus(
				new Node\Expr\Variable($node3->name),
				$this->currentUnpackNode->value
			);

			// @todo use array_splice?
			$plusOtherArrayItemsNode = new Node\Expr\AssignOp\Plus(
				new Node\Expr\Variable($node3->name),
				new Node\Expr\Array_($this->items)
			);

			$this->items = null;
			$this->currentUnpackNode = null;

			return [$node, $plusSpreadArrayNode, $plusOtherArrayItemsNode];
		}
	}
}
