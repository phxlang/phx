<?php

namespace Phx\Extension\Spread\Visitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use Phx\Common\NodeConnector;
use Phx\Extension\Spread\Helper\ArraySpreadHelper;
use Phx\Parser\Node\Expr\UnpackArrayItem;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class ArraySpreadVisitor extends NodeVisitorAbstract
{

	const PREFIX = '__phx_spread_';

	/**
     * @var \stdClass[]
     */
	private $arrayStack = [];

	/**
     * @var false|\stdClass
     */
	private $currentArray = false;

	/**
     * @var Node\Expr\Variable[]
     */
	private $uses = [];

    /**
     * @var array
     */
	private $pendingSplices = [];

    /**
     * @var null|Node\Expr\Array_
     */
	private $pendingArray = null;

    /**
     * @param Node $node
     * @return null
     */
	public function enterNode(Node $node)
	{
        if ($node instanceof Node\Expr\Array_) {
	        $this->arrayStack[] = $this->currentArray = (object) ['node' => $node, 'unpacks' => []];
        } elseif ($this->currentArray !== false && $node instanceof Node\Expr\Variable) {
            $this->uses[$node->name] = $node;
        }

		return null;
	}

    /**
     * @param Node $node
     * @return array|null|Node
     */
	public function leaveNode(Node $node)
	{
		if ($node instanceof UnpackArrayItem) {
			$id = self::PREFIX.uniqid();
			$this->currentArray->unpacks[$id] = $node;
			return new Node\Expr\ArrayItem(new Node\Scalar\String_($id));
		}

        if ($node === $this->pendingArray) {
            $replaces = $this->pendingSplices;

            $this->pendingArray = null;
            $this->pendingSplices = [];

            return $replaces;
        }

	    if (false === $node instanceof Node\Expr\Array_) {
	        return null;
        }

        /** @var Node\Expr\Array_ $node */

        if ([] === $this->currentArray->unpacks) {
            $this->leaveCurrentArray();
            return null;
        }

        $parentNode = $node->getAttribute(NodeConnector::ATTR_PARENT);

        if (true === $parentNode instanceof Node\Expr\Assign) {
            $arrayVar = new Node\Expr\Variable($node->getAttribute(NodeConnector::ATTR_PREV)->name);
        } else {
            // We need do create a dummy variable for the closure transformation used in case of "inline" arrays
            $arrayVar = new Node\Expr\Variable('array');
        }

        $splices = [
            new Node\Expr\Assign($arrayVar, $node)
        ];

        foreach ($this->currentArray->unpacks as $id => $unpack) {
            // Store uses in case we need to inject them into a closure
            if (true === $unpack->value instanceof Node\Expr\Variable) {
                $this->uses[$unpack->value->name] = $unpack->value;
            } elseif (
                true === $unpack->value instanceof  Node\Expr\MethodCall
                && true === $unpack->value->var instanceof Node\Expr\Variable
            ) {
                $this->uses[$unpack->value->var->name] = $unpack->value->var;
            }

            $splices[] = new Node\Expr\FuncCall(
                new Node\Name(ArraySpreadHelper::class.'::spreadArray'),
                [
                    $arrayVar,
                    new Node\Scalar\String_($id),
                    $unpack->value
                ]
            );
        }

        if (true === $parentNode instanceof Node\Expr\Assign) {
            $this->pendingSplices = $splices;
            $this->pendingArray = $parentNode;

            $this->leaveCurrentArray();

            return null;
        }

		$splices[] = new Node\Stmt\Return_($arrayVar);

        if (isset($this->uses['this'])) {
            unset($this->uses['this']);
        }

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
