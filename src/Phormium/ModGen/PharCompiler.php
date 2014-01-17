<?php

namespace Phormium\ModGen;

use DateTime;
use Phar;

use Symfony\Component\Finder\Finder;

class PharCompiler
{
    private $version;

    public function __construct()
    {
        $this->version = trim(`git describe`);
        if (empty($this->version)) {
            throw new \Exception("Unable to detect version.");
        }

        $date = trim(`git log -n1 --pretty=%ci HEAD`);
        if (empty($date)) {
            throw new \Exception("Unable to detect release date.");
        }
        $dt = new DateTime($date);
        $this->releaseDate = $dt->format("Y-m-d");
    }

    public function compile($target = "modgen.phar")
    {
        echo "Compiling PHAR for Phormium ModGen\n";
        echo "Version: {$this->version}\n";
        echo "Release date: {$this->releaseDate}\n";

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

        echo "Compiled: " . realpath($target) . "\n";
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

            // Add version and release date
            if ($path === 'src/Phormium/ModGen/Console/Application.php') {
                $contents = str_replace('@modgen_version@', $this->version, $contents);
                $contents = str_replace('@modgen_release_date@', $this->releaseDate, $contents);
            }

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
