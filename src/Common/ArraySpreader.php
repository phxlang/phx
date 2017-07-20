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
		/*if (false === $node instanceof UnpackArrayItem) {
			return null;
		}
		return NodeTraverser::REMOVE_NODE;*/
		if (null === $this->currentUnpackNode) {
			return null;
		}

		if ($node === $this->currentUnpackNode) {
			return NodeTraverser::REMOVE_NODE;
		}

		if ($node->getAttribute(NodeConnector::ATTR_PARENT) === $this->currentUnpackNode->getAttribute(NodeConnector::ATTR_PARENT)) {
			$this->items[] = $node;
			return NodeTraverser::REMOVE_NODE;
		} elseif ($node === $this->currentUnpackNode->getAttribute(NodeConnector::ATTR_PARENT)) {
			// end of array node -> append

			// append += $unpackArray

			/** @var Node $parent */
			$parent = $this->currentUnpackNode->getAttribute(NodeConnector::ATTR_PARENT);
			/** @var Node\Expr\Variable $node3 */
			$node3 = $parent->getAttribute(NodeConnector::ATTR_PREV);

			$plusSpreadArrayNode = new Node\Expr\AssignOp\Plus(
				new Node\Expr\Variable($node3->name),
				$this->currentUnpackNode->value
			);

			$node2 = new Node\Expr\AssignOp\Plus(
				new Node\Expr\Variable($node3->name),
				new Node\Expr\Array_($this->items)
			);

			$nodeDumper = new NodeDumper();
			var_dump($nodeDumper->dump($node->getAttributes(NodeConnector::ATTR_PARENT))); exit;
			/*var_dump($nodeDumper->dump($node));
			var_dump($nodeDumper->dump($node2)); exit;*/
			return [$plusSpreadArrayNode, $node2];
		}
	}
}
