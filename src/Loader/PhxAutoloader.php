<?php

namespace Phx\Loader;

use Composer\Autoload\ClassLoader;
use Phx\Common\Transpiler;
use Phx\Common\TranspilerBuilder;
use Phx\Lexer\PhxLexer;
use Phx\Parser\Phx;

/**
 * @author Pascal Muenst <pascal@timesplinter.ch>
 */
class PhxAutoloader extends ClassLoader
{

    /**
     * @var Transpiler
     */
    private $transpiler;

    /**
     * @var ClassLoader
     */
    private $loader;

    public function __construct(ClassLoader $loader)
    {
        $this->transpiler = $this->getTranspiler();
        $this->loader = $loader;
        $this->add(null, $loader->getFallbackDirs());
        $this->addPsr4(null, $loader->getFallbackDirsPsr4());
        foreach ($loader->getPrefixes() as $prefix => $path) {
            $this->add($prefix, $path);
        }
        foreach ($loader->getPrefixesPsr4() as $prefix => $path) {
            $this->addPsr4($prefix, $path);
        }
        $this->setUseIncludePath($loader->getUseIncludePath());
    }

    public function loadClass($class)
    {
        $method = new \ReflectionMethod(PhxAutoloader::class, 'findFileWithExtension');
        $method->setAccessible(true);
        $result = $method->invoke($this, $class, '.phx'); exit;

        //var_dump($result); exit;

        if ($file = $this->findFile($class)) {
            includeCode($file);
            die('iiiii');

            return true;
        }
    }

    private function getTranspiler()
    {
        return null;
    }
}

function includeCode($code) {
    eval($code);
}