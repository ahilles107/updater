<?php

namespace spec\Updater\Command;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\NullOutput;

class GeneratePackageCommandSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Updater\Command\GeneratePackageCommand');
    }

    public function it_generates_package()
    {
        $input = new ArrayInput(
            array(
                'reference' => '89144ee17ce72370766e21d1a767fdbed0a9e8b7',
                'source' => __DIR__ . '/../../../../updater',
                'target' => __DIR__ . '/../../packages/',
                'exclude' => array(
                    'schema/updater-schema.json',
                    'bin/phpunit',
                )
            ),
            new InputDefinition(array(
                new InputArgument('reference', InputArgument::REQUIRED, 'COMMIT or TAG'),
                new InputArgument('source', InputArgument::OPTIONAL, 'the source directory, defaults to current directory'),
                new InputArgument('target', InputArgument::OPTIONAL, 'the target directory, defaults to \'packages/\''),
                new InputArgument('exclude', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, 'files or directories to exclude from package')
            ))
        );

        $output = new NullOutput();
        $this->execute($input, $output)->shouldReturn(true);
    }
}
