<?php

namespace spec\Updater\Command;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Input\InputOption;

class UpdateCommandSpec extends ObjectBehavior
{
    private $arguments;

    private $definitions;

    public function it_is_initializable()
    {
        $this->shouldHaveType('Updater\Command\UpdateCommand');
    }

    public function let($die)
    {
        $this->arguments = array(
            'target' => __DIR__ . '/../../sample_app',
            'temp_dir' => __DIR__ . '/../../sample_app/cache',
            'package_dir' => __DIR__ . '/../../packages/update-4.3.1.zip',
            '--rollback'  => false,
        );

        $this->definitions = array(
            new InputArgument('target', InputArgument::REQUIRED, 'Your application directory you want to update'),
            new InputArgument('temp_dir', InputArgument::REQUIRED, 'Directory to your application temp/cache folder'),
            new InputArgument('package_dir', InputArgument::REQUIRED, 'Package real path (path to your zip package)'),
            new InputOption('rollback', null, InputOption::VALUE_NONE, 'If set, then changes will be rollbacked.'),
        );
    }

    public function it_do_update()
    {
        $input = new ArrayInput(
            $this->arguments,
            new InputDefinition($this->definitions)
        );

        $output = new NullOutput();
        $this->execute($input, $output)->shouldReturn(true);
    }

    public function it_do_rollback()
    {
        $this->arguments['--rollback'] = true;
        $input = new ArrayInput(
            $this->arguments,
            new InputDefinition($this->definitions)
        );

        $output = new NullOutput();
        $this->execute($input, $output)->shouldReturn(true);
    }
}
