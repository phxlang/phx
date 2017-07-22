<?php

namespace Phx\Tests;

use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use Phx\Common\PhxTranspilerBuilder;
use Phx\Common\Transpiler;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
abstract class PhxTestCase extends TestCase
{

    /**
     * @var Transpiler
     */
    protected static $transpiler;

    /**
     * @var array
     */
    private static $codes = [];

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$transpiler = PhxTranspilerBuilder::create()->build();

        $fileName = (new \ReflectionClass(get_called_class()))->getFileName();

        if (true === file_exists($codeFile = preg_replace('/.php$/', '.code', $fileName))) {
            self::$codes = self::loadCodes($codeFile);
        }
    }

    /**
     * @param array $code
     */
    public static function assertCode(array $code)
    {
        if (false === isset($code['phx']) || false === isset($code['output'])) {
            throw new Exception(
                'Code array supplied is in wrong format (expected format: [\'phx\' => ..., \'code\' => ...])'
            );
        }

        ob_start(function($buff) {
            return $buff;
        });

        self::execCode($code['phx']);
        $codeOutput = trim(ob_get_clean());
        //echo $codeOutput;
        self::assertEquals($code['output'], $codeOutput);
    }

    /**
     * @param string $expectedPhp
     * @param string $phxCode
     */
    public static function assertPhp(string $expectedPhp, string $phxCode)
    {
        self::assertEquals($expectedPhp, self::$transpiler->fromString('<?phx'.PHP_EOL.$phxCode));
    }

    /**
     * @param $code
     * @return void
     */
    private static function execCode($code)
    {
        eval(self::$transpiler->fromString('<?phx'.PHP_EOL.$code));
    }

    protected static function loadCodes(string $filePath): array
    {
        $codeFilePattern = '/(?<name>.+?)\n<<<<<<<\n(?<phx>.+?)\n=======\n(?<output>.+?)\n>>>>>>>\n*/s';

        if (0 === preg_match_all($codeFilePattern, file_get_contents($filePath), $matches, PREG_SET_ORDER)) {
            throw new Exception(sprintf('File %s is not in a valid format or empty', $filePath));
        }

        $codes = [];

        foreach($matches as $match) {
            $codes[$match['name']] = [
                'phx' => $match['phx'],
                'output' => $match['output']
            ];
        }

        return $codes;
    }

    /**
     * @param string $name
     * @return array
     */
    public function getCode(string $name): array
    {
        if (false === isset(self::$codes[$name])) {
            return null;
        }
        return self::$codes[$name];
    }
}