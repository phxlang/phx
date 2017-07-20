<?php

namespace Phx\Common;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use Phx\Parser\Node\Expr\UnpackArrayItem;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class ArraySpreader extends NodeVisitorAbstract
{
    /** @var UnpackArrayItem */
	private $currentUnpackArrayItem = null;

	private $lastIds = [];
	private $uses = [];

	public function enterNode(Node $node)
	{
        if ($node instanceof UnpackArrayItem) {
        	$this->currentUnpackArrayItem = $node;
        }

		return null;
	}

	public function leaveNode(Node $node)
	{
		if ($node instanceof UnpackArrayItem) {
			$id = '__spread_'.uniqid();
			$this->lastIds[$id] = $node;
			return new Node\Expr\ArrayItem(new Node\Scalar\String_($id));
		}

	    if (
	    	$this->currentUnpackArrayItem === null
		    || $node !== ($parent = $this->currentUnpackArrayItem->getAttribute(NodeConnector::ATTR_PARENT))
	    ) {
	        return null;
        }

		$splices = [
			new Node\Expr\Assign(
				new Node\Expr\Variable('array'),
				$parent
			),
		];

		foreach ($this->lastIds as $id => $node) {
			if ($node->value instanceof Node\Expr\Variable) {
				$this->uses[$node->value->name] = $node->value;
			}

			$splices[] = new Node\Expr\FuncCall(
				new Node\Name('array_splice'),
				[
					new Node\Expr\Variable('array'),
					new Node\Expr\FuncCall(
						new Node\Name('array_search'),
						[
							new Node\Scalar\String_($id),
							new Node\Expr\Variable('array'),
						]
					),
					new Node\Scalar\LNumber(1),
					$node->value
				]
			);
		}

		$splices[] = new Node\Stmt\Return_(
			new Node\Expr\Variable('array')
		);

		$node = new Node\Expr\FuncCall(
			new Node\Expr\Closure(
				[
					'uses' => array_values($this->uses),
					'stmts' => $splices
				]
			)
		);

		$this->lastIds = [];

		return $node;
	}
}
