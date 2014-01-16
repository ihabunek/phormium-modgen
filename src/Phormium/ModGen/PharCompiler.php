<?php

namespace Phormium\ModGen;

use Phar;

use Symfony\Component\Finder\Finder;

class PharCompiler
{
    public function compile($target = "modgen.phar")
    {
        $version = trim(`git describe`);
        if (empty($version)) {
            throw new \Exception("Unable to detect version.");
        }

        echo "Compiling PHAR for Modgen $version\n";

        if (file_exists($target)) {
            unlink($target);
        }

        $phar = new Phar($target);
        $phar->startBuffering();
        $this->addFiles($phar);
        $phar->setStub("<?php
            Phar::mapPhar('modgen.phar');
            require 'phar://modgen.phar/bin/modgen';
            __HALT_COMPILER();
        ?>");

        $phar->stopBuffering();

        echo "Compiled: $target\n";
    }

    private function addFiles($phar)
    {
        $base = realpath(__DIR__ . "/../../../");

        $finder = new Finder();
        $iterator = $finder
            ->files()
            ->name('*.php')
            ->in($base)
            ->path('/^src/')
            ->path('/^vendor/')
            ->notPath('vendor/symfony/finder');

        foreach ($iterator as $file) {
            $fullPath = $file->getRealPath();

            $path = str_replace($base, '', $fullPath);
            $path = strtr($path, "\\", "/");
            $path = ltrim($path, '/');

            $contents = file_get_contents($fullPath);
            $phar->addFromString($path, $contents);
        }

        // Add the executable
        $path = "bin/modgen";
        $contents = file_get_contents("$base/$path");

        // Remove shebang which interferes
        $contents = preg_replace('/^#!\/usr\/bin\/env php\s*/', '', $contents);
        $phar->addFromString($path, $contents);
    }
}
