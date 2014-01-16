<?php

namespace Phormium\ModGen\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;

class Application extends BaseApplication
{
    const VERSION = '@modgen_version@';
    const RELEASE_DATE = '@modgen_release_date@';

    private static $logo = '
    ____  __                         _
   / __ \/ /_  ____  _________ ___  (_)_  ______ ___
  / /_/ / __ \/ __ \/ ___/ __ `__ \/ / / / / __ `__ \
 / ____/ / / / /_/ / /  / / / / / / / /_/ / / / / / /
/_/   /_/ /_/\____/_/  /_/ /_/ /_/_/\__,_/_/ /_/ /_/
M o d e l   G e n e r a t o r
';

	/** Set project name and version. */
    public function __construct()
    {
        parent::__construct('Phormium Model Generator', self::VERSION);
    }

    /** Add logo to help text. */
    public function getHelp()
    {
        return self::$logo . "\n\n" . parent::getHelp();
    }

    public function getLongVersion()
    {
        return parent::getLongVersion() . sprintf(
            ' released on <comment>%s</comment>',
            self::RELEASE_DATE
        );
    }

	/**
     * Gets the default commands that should always be available.
     *
     * @return array An array of default Command instances
     */
    protected function getDefaultCommands()
    {
        $defaultCommands = parent::getDefaultCommands();
        $defaultCommands[] = new GenerateCommand();
        return $defaultCommands;
    }
}
