<?php

namespace Phormium\ModGen\Console;

use Phormium\DB;
use Phormium\ModGen\Generator;

use Symfony\Component\Console;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends Console\Command\Command
{
    protected function configure()
    {
        $this
            ->setName('modgen')
            ->setDescription('Generate model for a single table')
            ->addArgument(
                'database', InputArgument::REQUIRED,
                'Source database, as defined in the config file'
            )
            ->addArgument(
                'table', InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
                'One or more tables for which to generate a model'
            )
            ->addOption(
               'config', null, InputOption::VALUE_OPTIONAL,
               'Optional path to the config file.',
               'config.json'
            )
            ->addOption(
               'target', null, InputOption::VALUE_OPTIONAL,
               'Target folder where the model will be generated.',
               'target'
            )
            ->addOption(
               'namespace', null, InputOption::VALUE_OPTIONAL,
               'The namespace used for the model.',
               ''
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $database = $input->getArgument('database');
        $tables = $input->getArgument('table');

        $config = $input->getOption('config');
        $target = $input->getOption('target');
        $namespace = $input->getOption('namespace');

        if (!file_exists($config)) {
            throw new \Exception("Configuration file not found at [$config]");
        }

        if (!is_dir($target)) {
            $output->writeln("<info>Creating target directory [$target]</info>");
            if (!mkdir($target)) {
                throw new \Exception("Failed creating traget directory [$target]");
            }
        }

        $target = realpath($target);
        $config = realpath($config);

        $output->writeln("<info>Configuration:</info> $config");
        $output->writeln("<info>Target:</info> $target");
        if (!empty($namespace)) {
            $output->writeln("<info>Namespace:</info> $namespace");
        }
        $output->writeln("");

        DB::configure($config);

        $generator = new Generator($target, $namespace, $output);
        if (empty($tables)) {
            $inspector = $generator->getInspector($database);
            $tables = $inspector->getTables($database);
        }

        foreach ($tables as $table) {
            try
            {
                list($class, $path) = $generator->generateModel($database, $table, $namespace);
                $output->writeln("<info>Generated model for <comment>$database:$table</comment></info> <info>at</info> $path.");
            }
            catch (\Exception $ex)
            {
                $output->writeln("");
                $output->writeln("<error>Failed generation for $database:$table\n$ex</error>");
                $output->writeln("");
            }
        }
        $output->writeln("");
    }
}
