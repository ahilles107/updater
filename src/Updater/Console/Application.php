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
use Symfony\Component\Console\Input\InputInterface;

/**
 * Updater console application
 */
class Application extends BaseApplication
{
    const NAME = 'Updater Console Application';
    const VERSION = '0.1';

    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(static::NAME, static::VERSION);
    }
}
