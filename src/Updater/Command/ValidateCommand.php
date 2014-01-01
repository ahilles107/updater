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
use Updater\Tools\Json\JsonManager;

class ValidateCommand extends Command
{
    /**
     * configure
     */
    public function configure()
    {
        $this
            ->setName('validate')
            ->setDescription('Validates a update package')
            ->setDefinition(array(
                new InputArgument('file', InputArgument::REQUIRED, 'path to update package')
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
     * @return boolean
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');

        if (!file_exists($file)) {
            throw new \Exception($file . ' not found.', 1);
        }

        if (!is_readable($file)) {
            throw new \Exception($file . ' is not readable.', 1);
        }

        $jsonManager = new JsonManager();
        $schema = file_get_contents(realpath(__DIR__ . '/../../../schema/') . '/updater-schema.json');

        $json = $jsonManager->getJsonFromFile('update.json', realpath($file));
        $validationResult = $jsonManager->validateJson($json);

        if (true !== $validationResult) {
            $output->writeln('<error>* JSON inside update package isn\'t valid!</error>');
            $output->writeln('<error>'.$validationResult->getMessage().'<error>');

            return true;
        } else {
            $output->writeln('<info>* JSON inside update package is valid!</info>');
        }

        $schemaResult = $jsonManager->validateSchema($json, $schema);

        if (true !== $schemaResult) {
            $output->writeln('<error>* JSON does not validate!</error>');
        } else {
            $output->writeln('<info>* JSON validates against the schema!</info>');
        }

        $packageSpec = json_decode($json, true);
        $output->writeln('<info>Package version:</info>        '.$packageSpec['version']);
        $output->writeln('<info>Package description:</info>    '.$packageSpec['description']);
        $output->writeln('<info>Package mainatiner:</info>     '.$packageSpec['mainatiner']);
        $output->writeln('<info>Package changelog:</info>      '.implode(', ', $packageSpec['changelog']));

        $output->writeln('<info>* All valid!</info>');

        return true;
    }
}
