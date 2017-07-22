<?php

namespace Phx\Tests\Extension\Spread;

use Phx\Tests\PhxTestCase;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 * @group integration
 * @covers \Phx\Extension\Spread\Visitor\ArraySpreadVisitor
 * @covers \Phx\Extension\Spread\Helper\ArraySpreadHelper
 */
class SpreadExtensionTest extends PhxTestCase
{
    public function testPlainArrayWorks()
    {
        self::assertNotNull($code = self::getCode('testPlainArrayWorks'));
        self::assertCode($code);
    }

    public function testSimpleSpreadInsertsEveryArrayEntryIntoParentArray()
    {
        self::assertNotNull($code = self::getCode(
            'testSimpleSpreadInsertsEveryArrayEntryIntoParentArrayOnAssign'
        ));
        self::assertCode($code);
    }

    public function testSimpleSpreadInsertsEveryArrayEntryIntoParentArrayOnNoAssign()
    {
        self::assertNotNull($code = self::getCode(
            'testSimpleSpreadInsertsEveryArrayEntryIntoParentArrayOnNoAssign'
        ));
        self::assertCode($code);
    }

    public function testMultipleSimpleSpreadsGetReplacedInTheRightOrder()
    {
        self::assertNotNull($code = self::getCode(
            'testMultipleSimpleSpreadsGetReplacedInTheRightOrder'
        ));
        self::assertCode($code);
    }

    public function testSpreadNestedArrayPreserveInnerArray()
    {
        self::assertNotNull($code = self::getCode(
            'testSpreadNestedArrayPreserveInnerArray'
        ));
        self::assertCode($code);
    }

    public function testSpreadsInAllVariantsGetReplacedCorrectly()
    {
        self::assertNotNull($code = self::getCode(
            'testSpreadsInAllVariantsGetReplacedCorrectly'
        ));
        self::assertCode($code);
    }

    public function testDoesNotInjectThisIntoClosure()
    {
        self::assertNotNull($code = self::getCode(
            'testDoesNotInjectThisIntoClosure'
        ));
        self::assertCode($code);
    }
}
