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
    /** @var null|Node\Expr\Array_ */
	private $currentArrayNode = null;

	/** @var array */
	private $unpacks = [];

	/** @var int */
	private $pos = 0;

	/** @var array */
	private $arrayCounts = [];

	public function enterNode(Node $node)
	{
		// Take all further ArrayItems out of the array,
		// Append new statement for array += spreaded variable,
		// Append new array node with rest of ArrayItems
        if (true === $node instanceof Node\Expr\Array_) {
            $this->currentArrayNode = $node;

            // Keep track of array elements
            $var = $node->getAttribute(NodeConnector::ATTR_PREV);
            $this->arrayCounts[$var->name] = count($node->items);
        } elseif (true === $node instanceof UnpackArrayItem) {
            $this->unpacks[] = [
                'node' => $node,
                'pos' => $this->pos
            ];
        } elseif (true === $node instanceof Node\Expr\ArrayItem) {
            ++$this->pos;
        }

		return null;
	}

	public function leaveNode(Node $node) {
	    if ($node instanceof UnpackArrayItem) {
	        return NodeTraverser::REMOVE_NODE;
        } elseif ($this->currentArrayNode === null || $node !== $this->currentArrayNode->getAttribute(NodeConnector::ATTR_PARENT)) {
	        return null;
        }

        $sliceNodes = [$node];
	    $offset = 0;

        foreach ($this->unpacks as $unpack) {
            //echo 'do an unpack of ' , $unpack['node']->value->name , ' (count: ',$this->arrayCounts[$unpack['node']->value->name],') at pos ' ,  $unpack['pos'] , PHP_EOL;
            /** @var Node $parent */
			$prev = $this->currentArrayNode->getAttribute(NodeConnector::ATTR_PREV);

			$unpackNode = $unpack['node']->value;
			$unpackPos = $unpack['pos'];

            $sliceNodes[] = new Node\Expr\FuncCall(
                new Node\Name('array_splice'),
                [
                    $prev,
                    new Node\Scalar\LNumber($unpackPos+$offset),
                    new Node\Scalar\LNumber(0),
                    $unpackNode
                ]
            );

            $offset += $this->arrayCounts[$unpack['node']->value->name];
        }

        $this->currentArrayNode = null;
        $this->unpacks = [];
        $this->pos = 0;

        return $sliceNodes;
	}
}
