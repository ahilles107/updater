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
                'reference' => 'cc58654e857668108bfc926e1c8fbc9fd1013e02',
                'source' => __DIR__ . '/../../sample_app',
                'target' => __DIR__ . '/../../packages/'
            ),
            new InputDefinition(array(
                new InputArgument('reference', InputArgument::REQUIRED, 'COMMIT or TAG'),
                new InputArgument('source', InputArgument::OPTIONAL, 'the source directory, defaults to current directory'),
                new InputArgument('target', InputArgument::OPTIONAL, 'the target directory, defaults to \'packages/\'')
            ))
        );

        $output = new NullOutput();
        $this->execute($input, $output)->shouldReturn(true);
    }
}