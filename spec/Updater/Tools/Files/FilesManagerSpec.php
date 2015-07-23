<?php

namespace spec\Updater\Tools\Files;

use PhpSpec\ObjectBehavior;

class FilesManagerSpec extends ObjectBehavior
{
    private $packagesDir;

    private $reference;

    private $schemaFile;

    public function let()
    {
        $this->packagesDir = __DIR__.'/../../../packages/';
        $this->reference = '89144ee17ce72370766e21d1a767fdbed0a9e8b7';
        $this->schemaFile = realpath(__DIR__.'/../../../../schema/').'/updater-schema.json';
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Updater\Tools\Files\FilesManager');
    }

    public function it_loads_the_update_spec_from_a_zip_file()
    {
        $this->getSpecFromZip(realpath($this->packagesDir), 'update-4.3.1.zip')->shouldBeArray();
    }

    public function it_creates_json_file_from_schema()
    {
        $arguments = array(
            'reference' => $this->reference,
            'target' => $this->packagesDir,
            'version' => '4.3.1-RC',
            'description' => 'This is test package description',
            'maintainer' => 'Jhon Doe',
            'update-type' => 'security-bugfix',
            'include' => 'config/',
            'exclude' => array(
                'schema/updater-schema.json',
                'bin/phpunit',
            ),
        );

        $this->createJsonFileFromSchema($this->schemaFile, $arguments)->shouldReturn(true);
    }

    public function it_gets_file_content_to_array()
    {
        $this->getFileContent($this->reference, $this->packagesDir)->shouldBeArray();
    }

    public function it_exclude_dirs_from_diff_array()
    {
        $fileMapping = array(
            'A  bin/getChanged.sh',
            'A  bin/jsonlint',
            'A  bin/phpunit',
            'M  schema/updater-schema.json',
            'D  install/',
        );

        $excludes = array(
            'schema/updater-schema.json',
            'bin/phpunit',
        );

        $this->exclude($fileMapping, $excludes)->shouldBeArray();
    }
}
