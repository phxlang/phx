<?php

namespace Phx\Loader;

use Composer\Autoload\ClassLoader;
use PhpParser\PrettyPrinter\Standard;
use Phx\Common\Transpiler;
use Phx\Common\TranspilerBuilder;
use Phx\Extension\ForIn\ForInExtension;
use Phx\Extension\Spread\SpreadExtension;
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

    /**
     * @var \ReflectionMethod
     */
    private $findFileWithExtensionMethod;

    public function __construct(ClassLoader $loader)
    {
        $this->transpiler = $this->getTranspiler();
        $this->loader = $loader;

        $this->findFileWithExtensionMethod = new \ReflectionMethod(PhxAutoloader::class, 'findFileWithExtension');
        $this->findFileWithExtensionMethod->setAccessible(true);
    }

    public function loadClass($class)
    {
        if (true === $this->loader->loadClass($class)) {
            return true;
        }

        if (false !== $filePath = $this->findFileWithExtensionMethod->invoke($this->loader, $class, '.phx')) {
            includeCode($this->transpiler->fromFile($filePath));
            return true;
        }
    }

    private function getTranspiler(): Transpiler
    {
        return TranspilerBuilder::create()
            ->setParser(new Phx(new PhxLexer()))
            ->setPrinter(new Standard())
            ->registerExtension(new SpreadExtension())
            ->registerExtension(new ForInExtension())
            ->build();
    }
}

function includeCode($code) {
    eval($code);
}
