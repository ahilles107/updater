<?php

namespace spec\Updater\Tools\Json;

use PhpSpec\ObjectBehavior;

class JsonManagerSpec extends ObjectBehavior
{
    private $packagesDir;

    public function let()
    {
        $this->packagesDir = __DIR__ . '/../../../packages/';
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Updater\Tools\Json\JsonManager');
    }

    public function it_gives_me_json_from_zip_file()
    {
        $this->getJsonFromFile('update.json', realpath($this->packagesDir.'update-4.3.1.zip'))->shouldBeString();
    }

    public function it_gives_valid_json()
    {
        $json = $this->getJsonFromFile('update.json', realpath($this->packagesDir.'update-4.3.1.zip'));
        $this->validateJson($json->getWrappedObject())->shouldReturn(true);
    }

    public function it_gives_valid_schema()
    {
        $json = $this->getJsonFromFile('update.json', realpath($this->packagesDir.'update-4.3.1.zip'));
        $schema = file_get_contents(realpath(__DIR__ . '/../../../../schema/') . '/updater-schema.json');

        $this->validateSchema($json, $schema)->shouldReturn(true);
    }

    public function it_adds_json_file_to_zip_archive()
    {
        $this->addJsonToFile('update.json', realpath($this->packagesDir.'update-4.3.1.zip'))->shouldReturn(true);
    }
}
