<?php

namespace Phx\Extension\NullableType;

use PhpParser\NodeVisitor;
use Phx\Extension\NullableType\Visitor\NullableTypeVisitor;
use Phx\Extension\VisitorExtension;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class NullableTypeExtension implements VisitorExtension
{

    /**
     * @param int $phpVersion
     * @return bool
     */
    public function supports(int $phpVersion): bool
    {
        return ($phpVersion >= 70000 && $phpVersion < 70100);
    }

    /**
     * @return NodeVisitor[]
     */
    public function getVisitors(): array
    {
        return [
            new NullableTypeVisitor()
        ];
    }
}
