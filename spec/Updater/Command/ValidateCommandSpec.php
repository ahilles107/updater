<?php

namespace spec\Updater\Command;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\NullOutput;

class ValidateCommandSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Updater\Command\ValidateCommand');
    }

    function it_is_valid_package()
    {
        $input = new ArrayInput(
            array('file' => realpath(__DIR__ . '/../../../spec/packages').'/update-4.3.1.zip'),
            new InputDefinition(array(new InputArgument('file', InputArgument::REQUIRED, 'path to update package')))
        );
        $output = new NullOutput();

        $this->execute($input, $output)->shouldReturn(true);
    }

    function it_is_invalid_package()
    {
        $input = new ArrayInput(
            array('file' => realpath(__DIR__ . '/../../../spec/packages').'/update-4.3.2.zip'),
            new InputDefinition(array(new InputArgument('file', InputArgument::REQUIRED, 'path to update package')))
        );
        $output = new NullOutput();

        $this->shouldThrow('\Exception')->during('execute', array($input, $output));
    }
}
