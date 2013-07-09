<?php

namespace Phormium\ModGen\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;

class Application extends BaseApplication
{
    private static $logo = <<<EOD
    ____  __                         _
   / __ \/ /_  ____  _________ ___  (_)_  ______ ___
  / /_/ / __ \/ __ \/ ___/ __ `__ \/ / / / / __ `__ \
 / ____/ / / / /_/ / /  / / / / / / / /_/ / / / / / /
/_/   /_/ /_/\____/_/  /_/ /_/ /_/_/\__,_/_/ /_/ /_/
EOD;

	/** Set project name and version. */
    public function __construct()
    {
        parent::__construct('Phormium model generator', 'dev-master');
    }

    /** Add logo to help text. */
    public function getHelp()
    {
        return self::$logo . "\n\n" . parent::getHelp();
    }

    /** Set default command. */
	protected function getCommandName(InputInterface $input)
    {
        return 'modgen';
    }

	/**
     * Gets the default commands that should always be available.
     *
     * @return array An array of default Command instances
     */
    protected function getDefaultCommands()
    {
        $defaultCommands = parent::getDefaultCommands();
        $defaultCommands[] = new Command();
        return $defaultCommands;
    }

	/**
     * Overridden so that the application doesn't expect the command
     * name to be the first argument.
     */
    public function getDefinition()
    {
        $inputDefinition = parent::getDefinition();
        $inputDefinition->setArguments();
        return $inputDefinition;
    }
}
