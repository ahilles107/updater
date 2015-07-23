<?php

namespace spec\Updater\Command;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\NullOutput;

class ValidateCommandSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Updater\Command\ValidateCommand');
    }

    public function it_is_valid_package()
    {
        $input = new ArrayInput(
            array('file' => realpath(__DIR__.'/../../../spec/packages').'/1.0.0.zip'),
            new InputDefinition(array(new InputArgument('file', InputArgument::REQUIRED, 'path to update package')))
        );
        $output = new NullOutput();

        $this->execute($input, $output)->shouldReturn(true);
    }
}
