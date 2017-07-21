<?php

namespace Phx\Extension\Spread;

use PhpParser\NodeVisitor;
use Phx\Common\NodeConnector;
use Phx\Extension\Spread\Visitor\ArraySpreadVisitor;
use Phx\Extension\VisitorExtension;
use Phx\Extension\YaccExtension;
use Phx\Parser\Node\Expr\UnpackArrayItem;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class SpreadExtension implements YaccExtension, VisitorExtension
{

	/**
	 * @return array
	 */
	public function extendYacc(): array
	{
		return [
			'array_pair' => [
				'T_ELLIPSIS expr' => '{ $$ = '.UnpackArrayItem::class.'[$2, null, false]; }'
			]
		];
	}

	/**
	 * @return NodeVisitor[]
	 */
	public function getVisitors(): array
	{
		return [
		    new NodeConnector(),
			new ArraySpreadVisitor()
		];
	}
}
