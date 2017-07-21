<?php

namespace Phx\Extension\Spread\Visitor;

use PhpParser\Node;
use PhpParser\NodeDumper;
use PhpParser\NodeVisitorAbstract;
use Phx\Common\NodeConnector;
use Phx\Parser\Node\Expr\UnpackArrayItem;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class ArraySpreadVisitor extends NodeVisitorAbstract
{

	const PREFIX = '__phx_spread_';

	/** @var \stdClass[] */
	private $arrayStack = [];

	/** @var null|\stdClass */
	private $currentArray = false;

	/** @var array */
	private $uses = [];

	private $dumpedArraySplices = [];

	private $dumpedArray = null;

	public function enterNode(Node $node)
	{
        if ($node instanceof Node\Expr\Array_) {
	        $this->arrayStack[] = $this->currentArray = (object) ['node' => $node, 'unpacks' => []];
        }

		return null;
	}

	public function leaveNode(Node $node)
	{
		if ($node instanceof UnpackArrayItem) {
			$id = self::PREFIX.uniqid();
			$this->currentArray->unpacks[$id] = $node;
			return new Node\Expr\ArrayItem(new Node\Scalar\String_($id));
		}

        if ($node === $this->dumpedArray) {
            $replaces = array_merge([$this->dumpedArray], $this->dumpedArraySplices);

            $this->dumpedArray = null;
            $this->dumpedArraySplices = [];

            return $replaces;
        }

	    if (false === $node instanceof Node\Expr\Array_) {
	        return null;
        }

        /** @var Node\Expr\Array_ $node */

        // @todo better blacklist than whitelist!!!
        if (($containingNode = $node->getAttribute(NodeConnector::ATTR_PARENT)) instanceof Node\Expr\Assign) {
            if (0 === count($this->currentArray->unpacks)) {
                $this->leaveCurrentArray();
                return null;
            }

            foreach ($this->currentArray->unpacks as $id => $unpack) {
                $var = new Node\Expr\Variable($node->getAttribute(NodeConnector::ATTR_PREV)->name);

                $this->dumpedArraySplices[] = new Node\Expr\FuncCall(
                    new Node\Name('array_splice'),
                    [
                        $var,
                        new Node\Expr\FuncCall(
                            new Node\Name('array_search'),
                            [
                                new Node\Scalar\String_($id),
                                $var,
                            ]
                        ),
                        new Node\Scalar\LNumber(1),
                        $unpack->value
                    ]
                );
            }

            $this->leaveCurrentArray();

            $this->dumpedArray = $containingNode;

            return null;
        }

        // Closure transformation for "inline" arrays
		$tmpArray = new Node\Expr\Variable('array');

		$splices = [
			new Node\Expr\Assign(
                $tmpArray,
				$node
			),
		];

		if ([] === $this->currentArray->unpacks) {
            $this->leaveCurrentArray();
		    return null;
        }

		foreach ($this->currentArray->unpacks as $id => $unpack) {
			if (true === $unpack->value instanceof Node\Expr\Variable) {
				$this->uses[$unpack->value->name] = $unpack->value;
			} elseif (
			    true === $unpack->value instanceof  Node\Expr\MethodCall
                && true === $unpack->value->var instanceof Node\Expr\Variable
            ) {
                $this->uses[$unpack->value->name] = $unpack->value->var;
            }

			$splices[] = new Node\Expr\FuncCall(
				new Node\Name('array_splice'),
				[
					$tmpArray,
					new Node\Expr\FuncCall(
						new Node\Name('array_search'),
						[
							new Node\Scalar\String_($id),
							$tmpArray,
						]
					),
					new Node\Scalar\LNumber(1),
					$unpack->value
				]
			);
		}

		$splices[] = new Node\Stmt\Return_($tmpArray);

		$node = new Node\Expr\FuncCall(
			new Node\Expr\Closure(
				[
					'uses' => array_values($this->uses),
					'stmts' => $splices
				]
			)
		);

		$this->leaveCurrentArray();

		return $node;
	}

    /**
     * @return void
     */
	private function leaveCurrentArray()
    {
        array_pop($this->arrayStack);

        if (false === $this->currentArray = end($this->arrayStack)) {
            $this->uses = [];
        }
    }
}
