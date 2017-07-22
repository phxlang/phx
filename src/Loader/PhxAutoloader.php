<?php

namespace Phx\Loader;

use Composer\Autoload\ClassLoader;
use Phx\Common\PhxTranspilerBuilder;
use Phx\Common\Transpiler;

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
        $this->transpiler = PhxTranspilerBuilder::create()->build();
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
}

function includeCode($code) {
    eval($code);
}
