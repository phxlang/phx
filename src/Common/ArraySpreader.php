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
    /** @var NodeRef[] */
	private $currentArrayNodes = [];

	/** @var null|NodeRef */
	private $currentArrayNode = null;

	/** @var array */
	private $unpacks = [];

	public function enterNode(Node $node)
	{
        if (true === $node instanceof Node\Expr\Array_) {
            //var_dump('uhh oh I found an array: ' . count($node->items));
            $this->currentArrayNode = $this->currentArrayNodes[] = new NodeRef($node);
            //echo 'array stack: ' , count($this->currentArrayNodes) , PHP_EOL;
        }

		return null;
	}

	public function leaveNode(Node $node) {
	    $dumper = new NodeDumper();
	    if ($node instanceof UnpackArrayItem) {
	        $id = '__spread_'.uniqid();
            $this->currentArrayNode->unpacks[$id] = $node;

            echo 'spread: ',$id,PHP_EOL;

            //var_dump($id, $dumper->dump($node)); echo '------------------------>',PHP_EOL;

            return new Node\Expr\ArrayItem(new Node\Scalar\String_($id));
        } elseif ($this->currentArrayNode === null || $node !== $this->currentArrayNode->node->getAttribute(NodeConnector::ATTR_PARENT)) {
	        if ($this->currentArrayNode !== null) {
                echo 'skip that: ', get_class($this->currentArrayNode->node->getAttribute(NodeConnector::ATTR_PARENT)), PHP_EOL;
            }
            return null;
        }
        echo $dumper->dump($node);
        $spliceNodes = [$node];
foreach ($this->currentArrayNode->unpacks as $id => $unpackNode) {
    echo PHP_EOL,'----------',PHP_EOL; var_dump($id, $dumper->dump($unpackNode));
}
        echo PHP_EOL,'------------------------>',PHP_EOL;
        foreach ($this->currentArrayNode->unpacks as $id => $unpack) {
            /** @var Node $parent */
			$prev = $this->currentArrayNode->node->getAttribute(NodeConnector::ATTR_PREV);

			$unpackNode = $unpack->value;

            $spliceNodes[] = new Node\Expr\FuncCall(
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

        array_pop($this->currentArrayNodes);

        if (false === $this->currentArrayNode = end($this->currentArrayNodes)) {
            $this->currentArrayNode = null;
        }

        return $spliceNodes;
	}
}

class NodeRef
{
    /** @var Node\Expr\Array_ */
    public $node = null;

    /** @var array UnpackArrayItem[] */
    public $unpacks = [];

    /**
     * NodeRef constructor.
     * @param null $node
     */
    public function __construct($node)
    {
        $this->node = $node;
    }
}