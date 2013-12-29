<?php

/*
 * This file is part of Updater.
 *
 * (c) Paweł Mikołajczuk <mikolajczuk.private@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Updater\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

class ValidateCommand extends Command
{
    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('validate')
            ->setDescription('Validates a update package')
            ->setDefinition(array(
                new InputArgument('file', InputArgument::OPTIONAL, 'path to update package', './update.zip')
            ))
            ->setHelp(
<<<EOT
The validate command validates a given update zip package.

EOT
            );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');
    }
}
