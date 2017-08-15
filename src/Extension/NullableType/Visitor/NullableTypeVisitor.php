<?php

namespace Phx\Extension\NullableType\Visitor;

use Phx\Extension\NullableType\Helper\NullableTypeHelper;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class NullableTypeVisitor extends NodeVisitorAbstract
{

    /**
     * @var null|Node\Stmt\ClassMethod
     */
    private $currentMethod = null;

    /**
     * @var array|Node\Param
     */
    private $params = [];

    /**
     * @var null|Node\NullableType
     */
    private $returnType = null;

    /**
     * @param Node $node
     * @return null
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Param && $node->type instanceof Node\NullableType) {
            $this->params[] = $node;
        } elseif ($node instanceof Node\Stmt\ClassMethod) {
            $this->currentMethod = $node;
            if ($node->returnType instanceof Node\NullableType) {
                $this->returnType = $node->returnType;
                $node->returnType = null;
            }
        }

        return null;
    }

    public function leaveNode(Node $node)
    {
        if ($node === $this->currentMethod) {
            $paramChecks = [];

            $i = 1;
            foreach ($this->params as $param) {
                $paramChecks[] = $this->getParamCheck($param, $i);
                $param->type = null;
                ++$i;
            }

            $this->currentMethod->stmts = array_merge($paramChecks, $this->currentMethod->stmts);
            $this->currentMethod = null;
        }
    }

    private function getParamCheck(Node\Param $param, int $argNo)
    {

        if (true === NullableTypeHelper::isNonObjectType($param->type->type)) {
            $typeNode = new Node\Scalar\String_($param->type->type);
        } else {
            $typeNode = new Node\Expr\ClassConstFetch(new Node\Name($param->type->type), 'class');
        }

        $param->type->setAttribute('parent', null);
        $node = new Node\Stmt\If_(
            new Node\Expr\BinaryOp\BooleanAnd(
                // $param !== null
                new Node\Expr\BinaryOp\NotIdentical(
                    new Node\Expr\Variable($param->name),
                    new Node\Expr\ConstFetch(new Node\Name('null'))
                ),
                // $param !== type
                new Node\Expr\BinaryOp\Identical(
                    new Node\Expr\ConstFetch(new Node\Name('false')),
                    new Node\Expr\FuncCall(
                        new Node\Name('\\' . NullableTypeHelper::class.'::isType'),
                        [
                            new Node\Expr\Variable($param->name),
                            $typeNode
                        ]
                    )
                )
            ),
            [
                'stmts' => [
                    new Node\Stmt\Throw_(
                        new Node\Expr\New_(
                            new Node\Name('\\' . \TypeError::class),
                            [
                                $this->concatNodes([
                                    new Node\Scalar\String_('Argument '.$argNo.' passed to '),
                                    new Node\Scalar\MagicConst\Method(),
                                    new Node\Scalar\String_('() must be of the type '),
                                    $typeNode,
                                    new Node\Scalar\String_(', '),
                                    new Node\Expr\FuncCall(
                                        new Node\Name('\\' . NullableTypeHelper::class . '::getType'),
                                        [
                                            new Node\Expr\Variable($param->name),
                                        ]
                                    ),
                                    new Node\Scalar\String_(' given, called'),
                                ])
                            ]
                        )
                    )
                ]
            ]
        );

        return $node;
    }

    /**
     * @param array|Node\Expr[] $concats
     * @return Node\Expr
     */
    private function concatNodes(array $concats): Node\Expr
    {
        $lastExpr = array_pop($concats);

        if (0 === count($concats)) {
            return $lastExpr;
        }

        return new Node\Expr\BinaryOp\Concat($this->concatNodes($concats), $lastExpr);
    }
}