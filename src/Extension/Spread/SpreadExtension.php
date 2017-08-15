<?php

namespace Phx\Extension\Spread;

use PhpParser\NodeVisitor;
use Phx\Common\NodeConnector;
use Phx\Extension\RuleExtension;
use Phx\Extension\Spread\Visitor\ArraySpreadVisitor;
use Phx\Extension\VisitorExtension;
use Phx\Extension\YaccExtension;
use Phx\Parser\Node\Expr\UnpackArrayItem;
use Phx\Yacc\Parser\Action;
use Phx\Yacc\Parser\Rule;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class SpreadExtension implements RuleExtension, VisitorExtension
{

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

    /**
     * @param array $ruleGroups
     * @return void
     */
    public function modifyYaccRules(array &$ruleGroups)
    {
        foreach ($ruleGroups as $group) {
            if ($group->name !== 'array_pair') {
                continue;
            }

            $group->list[] = new Rule(
                'T_ELLIPSIS expr',
                new Action('$$ = \\'.UnpackArrayItem::class.'[$2, null, false];', [])
            );
            break;
        }
    }

    /**
     * @param int $phpVersion
     * @return bool
     */
    public function supports(int $phpVersion): bool
    {
        return true;
    }
}
