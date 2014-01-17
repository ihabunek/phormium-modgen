<?php

namespace Phormium\ModGen\Console;

use Phormium\DB;
use Phormium\ModGen\Generator;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('generate')
            ->setDescription('Generate model classes for one or more tables')
            ->addArgument(
                'database', InputArgument::REQUIRED,
                'Source database, as defined in the config file'
            )
            ->addArgument(
                'table', InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
                'One or more tables for which to generate model classes'
            )
            ->addOption(
               'config', null, InputOption::VALUE_OPTIONAL,
               'Path to the Phormium configuration file',
               'config.json'
            )
            ->addOption(
               'target', null, InputOption::VALUE_OPTIONAL,
               'Target folder where the model classes will be generated. ' .
               'If not given, defaults to the current working directory.'
            )
            ->addOption(
               'namespace', null, InputOption::VALUE_OPTIONAL,
               'The namespace used for the model classes.',
               ''
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->loadConfig($input, $output);

        $database = $input->getArgument('database');
        $tables = $input->getArgument('table');
        $target = $this->getTarget($input, $output);

        $namespace = $input->getOption('namespace');
        if (!empty($namespace)) {
            $output->writeln("<info>Namespace:</info> $namespace");
        }
        $output->writeln("");

        $generator = new Generator($target, $namespace);
        if (empty($tables)) {
            $inspector = $generator->getInspector($database);
            $tables = $inspector->getTables($database);
        }

        foreach ($tables as $table) {
            try {
                list($class, $path) = $generator->generateModel($database, $table, $namespace);
                $output->writeln("<info>Generated model for <comment>$database:$table</comment></info> <info>at</info> $path.");
            } catch (\Exception $ex) {
                $msg = $ex->getMessage();
                $output->writeln("<error>Failed generation for $database:$table - $msg</error>");
            }
        }
        $output->writeln("");
    }

    protected function loadConfig(InputInterface $input, OutputInterface $output)
    {
        $config = $input->getOption('config');
        if (!file_exists($config)) {
            throw new \Exception("Configuration file not found at [$config]");
        }

        $config = realpath($config);

        $output->writeln("<info>Configuration:</info> $config");
        DB::configure($config);
    }

    protected function getTarget(InputInterface $input, OutputInterface $output)
    {
        $target = $input->getOption('target');

        // Default to current working directory
        if (empty($target)) {
            $target = getcwd();
        }

        if (!is_dir($target)) {
            $output->writeln("<info>Creating target directory [$target]</info>");
            if (!mkdir($target)) {
                throw new \Exception("Failed creating traget directory [$target]");
            }
        }

        $target = realpath($target);
        $output->writeln("<info>Target:</info> $target");
        return $target;
    }
}
