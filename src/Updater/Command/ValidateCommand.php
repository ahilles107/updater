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
use Updater\Tools\Json\JsonException;

class ValidateCommand extends Command
{
    /**
     * configure.
     */
    public function configure()
    {
        $this
            ->setName('validate')
            ->setDescription('Validates a update package')
            ->setDefinition(array(
                new InputArgument('file', InputArgument::REQUIRED, 'path to update package'),
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
     * @return bool
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $filePath = $input->getArgument('file');

        if (!file_exists($filePath)) {
            throw new \Exception($filePath.' not found.', 1);
        }

        if (!is_readable($filePath)) {
            throw new \Exception($filePath.' is not readable.', 1);
        }

        $jsonManager = new JsonManager($filePath);
        $schemaFile = realpath(__DIR__.'/../../../schema/').'/updater-schema.json';
        $schema = file_get_contents($schemaFile);

        $json = $jsonManager->getJsonFromFile();
        try {
            $jsonManager->validateJson($json);
            $jsonManager->validateSchema($json, $schema);
        } catch (JsonException $e) {
            $output->writeln('<comment>'.$e->getMessage().'</comment>');
            foreach ((array) $e->getErrors() as $error) {
                $output->writeln('<error>'.$error.'</error>');
            }

            return false;
        }

        $output->writeln('<info>* Syntax of a JSON string is valid!</info>');
        $output->writeln('<info>* JSON validates against the schema!</info>');

        $packageSpec = json_decode($json, true);
        $output->writeln('<info>Package version:</info>        '.$packageSpec['version']);
        $output->writeln('<info>Package description:</info>    '.$packageSpec['description']);
        $output->writeln('<info>Package maintainer:</info>     '.$packageSpec['maintainer']);
        $output->writeln('<info>Package changelog:</info>      '.implode(', ', $packageSpec['changelog']));

        $output->writeln('<info>* All is valid!</info>');

        return true;
    }
}
