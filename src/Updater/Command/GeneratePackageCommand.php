<?php

/*
 * This file is part of Updater.
 *
 * (c) Paweł Mikołajczuk <mikolajczuk.private@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 */

namespace Updater\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Process\Process;
use Updater\Tools\Files\FilesManager;

class GeneratePackageCommand extends Command
{
    /**
     * Target path, where package will be generated to.
     *
     * @var string
     */
    private $target = '/../../../packages/';

    /**
     * Bash scripts directory.
     *
     * @var string
     */
    private $scriptsDir = '/../../../bin/';

    /**
     * Configure console command.
     */
    public function configure()
    {
        $this
            ->setName('generate')
            ->setDescription('Generates update package')
            ->setDefinition(array(
                new InputArgument('reference', InputArgument::REQUIRED, 'COMMIT or TAG'),
                new InputArgument('version', InputArgument::REQUIRED, 'Release version'),
                new InputArgument('description', InputArgument::REQUIRED, 'Release description'),
                new InputArgument('maintainer', InputArgument::REQUIRED, 'Package mainatainer'),
                new InputArgument('update-type', InputArgument::REQUIRED, 'Update package type (e.g. minor, critical etc.'),
                new InputArgument('source', InputArgument::OPTIONAL, 'the source directory, defaults to current directory'),
                new InputArgument('target', InputArgument::OPTIONAL, 'the target directory, defaults to \'packages/\''),
                new InputArgument('comparePath', InputArgument::OPTIONAL, 'path in the repository from which you want to generate a package, defaults "./"'),
                new InputArgument('include', InputArgument::OPTIONAL, 'directory you want to include in addition (e.g. config folder)'),
                new InputArgument('exclude', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, 'files or directories to exclude from package'),
            ))
            ->setHelp(
<<<EOT
This command allows you to create an update package in the target directory from given source
based on the differences in a git repository between the current state and a
specific git tree-ish. You can also exclude files and/or directories from update package.

EOT
            );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $arguments = $input->getArguments();
        $filesManager = new FilesManager();
        $arguments = $this->validateArguments($arguments);
        $commandLine = 'bash '.realpath(__DIR__.$this->scriptsDir).'/getChanged.sh -n "'.$arguments['version'].'"';
        $commandLine = $this->generateParameters($commandLine, $arguments);
        $process = new Process($commandLine);
        $process->run(function ($type, $buffer) use ($output) {
            if (Process::ERR === $type) {
                $output->writeln('<error>'.$buffer.'</error>');
            } else {
                $output->writeln('<info>'.$buffer.'</info>');
            }
        });

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        if ($filesManager->createJsonFileFromSchema(realpath(__DIR__.FilesManager::SCHEMA_FILE_PATH), $arguments)) {
            return true;
        }

        return false;
    }

    private function validateArguments(array $arguments)
    {
        if (empty($arguments['target'])) {
            $arguments['target'] = realpath(__DIR__.$this->target).'/';
        }

        if (isset($arguments['exclude']) && empty($arguments['exclude'])) {
            $arguments['exclude'] = array();
        }

        if (!file_exists($arguments['target'])) {
            throw new \Exception($arguments['target'].' not found.', 1);
        }

        if (!is_writable($arguments['target'])) {
            throw new \Exception($arguments['target'].' is not writable.', 1);
        }

        return $arguments;
    }

    private function generateParameters($commandLine, array $arguments)
    {
        if (isset($arguments['comparePath']) && !empty($arguments['comparePath'])) {
            $commandLine .= ' -d '.$arguments['comparePath'];
        } else {
            $commandLine .= ' -d ./';
        }

        if (isset($arguments['source']) && !empty($arguments['source'])) {
            $commandLine .= ' -s '.$arguments['source'];
        }

        if (isset($arguments['target']) && !empty($arguments['target'])) {
            $commandLine .= ' -t '.$arguments['target'];
        }

        if (!empty($arguments['exclude'])) {
            $commandLine .= ' -e '.'"'.implode('|', $arguments['exclude']).'"';
        }

        $commandLine .= ' -c '.$arguments['reference'];

        return $commandLine;
    }
}
