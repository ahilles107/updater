<?php

namespace spec\Updater\Command;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\NullOutput;

class GeneratePackageCommandSpec extends ObjectBehavior
{
    private $arguments;

    private $definitions;

    public function it_is_initializable()
    {
        $this->shouldHaveType('Updater\Command\GeneratePackageCommand');
    }

    public function let($die)
    {
        $this->arguments = array(
            'reference' => '6989311ef1d2370897822dc190e06d9be247b668',
            'version' => '1.0.0',
            'description' => 'This is test package description',
            'maintainer' => 'Jhon Doe',
            'update-type' => 'security-bugfix',
            'source' => __DIR__.'/../../sample_app/',
            'target' => __DIR__.'/../../packages/',
            'include' => 'config/',
            'exclude' => array(
                'README.md',
                'LICENSE',
            ),
        );

        $this->definitions = new InputDefinition(array(
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
        ));
    }

    public function it_generates_package()
    {
        $input = new ArrayInput(
            $this->arguments,
            $this->definitions
        );

        $output = new NullOutput();
        $this->execute($input, $output)->shouldReturn(true);
    }

    public function it_fails_to_generate_package()
    {
        $this->arguments['target'] = __DIR__.'/../../wrong_directory/';
        $input = new ArrayInput(
            $this->arguments,
            $this->definitions
        );

        $output = new NullOutput();

        $this
        ->shouldThrow('\Exception')
        ->duringExecute($input, $output);
    }

    public function it_fails_to_generate_package_when_specified_source_does_not_exist()
    {
        $this->arguments['source'] = __DIR__.'/../../wrong_source_directory/';
        $input = new ArrayInput(
            $this->arguments,
            $this->definitions
        );

        $output = new NullOutput();

        $this
        ->shouldThrow('\RuntimeException')
        ->duringExecute($input, $output);
    }
}
