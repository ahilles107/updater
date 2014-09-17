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
        $this->packagesDir = __DIR__ . '/../../../packages/';
        $this->reference = '89144ee17ce72370766e21d1a767fdbed0a9e8b7';
        $this->schemaFile = realpath(__DIR__ . '/../../../../schema/') . '/updater-schema.json';
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
        $this->createJsonFileFromSchema($this->schemaFile, $this->reference, $this->packagesDir)->shouldReturn(true);
    }

    public function it_finds_generated_txt_diff_file()
    {

        $this->findDiffFile($this->reference, $this->packagesDir)->shouldBeArray();
    }
}
