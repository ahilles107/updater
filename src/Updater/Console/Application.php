<?php

/*
 * This file is part of Updater.
 *
 * (c) Paweł Mikołajczuk <mikolajczuk.private@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Updater\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Updater\Command;

/**
 * Updater console application.
 */
class Application extends BaseApplication
{
    const NAME = 'Updater Console Application';

    const VERSION = '0.1';

    private static $logo = "
  _    _               _           _
 | |  | |             | |         | |
 | |  | |  _ __     __| |   __ _  | |_    ___   _ __
 | |  | | | '_ \   / _` |  / _` | | __|  / _ \ | '__|
 | |__| | | |_) | | (_| | | (_| | | |_  |  __/ | |
  \____/  | .__/   \__,_|  \__,_|  \__|  \___| |_|
          | |
          |_|

";

    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(static::NAME, static::VERSION);
    }

    /**
     * Initializes all the composer commands.
     */
    protected function getDefaultCommands()
    {
        $commands = parent::getDefaultCommands();
        $commands[] = new Command\ValidateCommand();
        $commands[] = new Command\GeneratePackageCommand();
        $commands[] = new Command\UpdateCommand();

        return $commands;
    }

    /**
     * @inheritdoc
     */
    public function getHelp()
    {
        return self::$logo.parent::getHelp();
    }
}
