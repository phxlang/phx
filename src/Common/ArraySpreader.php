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

	public function enterNode(Node $node)
	{
        if (true === $node instanceof Node\Expr\Array_) {
            $this->currentArrayNode = $node;
        }

		return null;
	}

	public function leaveNode(Node $node) {
	    if ($node instanceof UnpackArrayItem) {
	        $id = '__spread_'.uniqid();
            $this->unpacks[$id] = $node;

            return new Node\Expr\ArrayItem(new Node\Scalar\String_($id));
        } elseif ($this->currentArrayNode === null || $node !== $this->currentArrayNode->getAttribute(NodeConnector::ATTR_PARENT)) {
	        return null;
        }

        $sliceNodes = [$node];

        foreach ($this->unpacks as $id => $unpack) {
            /** @var Node $parent */
			$prev = $this->currentArrayNode->getAttribute(NodeConnector::ATTR_PREV);

			$unpackNode = $unpack->value;

            $sliceNodes[] = new Node\Expr\FuncCall(
                new Node\Name('array_splice'),
                [
                    $prev,
                    new Node\Expr\FuncCall(
                        new Node\Name('array_search'),
                        [
                            new Node\Scalar\String_($id),
                            $prev,
                        ]
                    ),
                    new Node\Scalar\LNumber(1),
                    $unpackNode
                ]
            );
        }

        $this->currentArrayNode = null;
        $this->unpacks = [];

        return $sliceNodes;
	}
}
