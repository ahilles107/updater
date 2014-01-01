<?php

namespace spec\Updater\Tools\Json;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class JsonManagerSpec extends ObjectBehavior
{
    private $packagesDir;

    function let()
    {
        $this->packagesDir = __DIR__ . '/../../../packages/';
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Updater\Tools\Json\JsonManager');
    }

    function it_gives_me_json_from_zip_file()
    {
        $this->getJsonFromFile('update.json', realpath($this->packagesDir.'update-4.3.1.zip'))->shouldBeString();
    }

    function it_gives_valid_json()
    {
        $json = $this->getJsonFromFile('update.json', realpath($this->packagesDir.'update-4.3.1.zip'));
        $this->validateJson($json->getWrappedObject())->shouldReturn(true);
    }

    function it_gives_valid_schema()
    {
        $json = $this->getJsonFromFile('update.json', realpath($this->packagesDir.'update-4.3.1.zip'));
        $schema = file_get_contents(realpath(__DIR__ . '/../../../../schema/') . '/updater-schema.json');

        $this->validateSchema($json, $schema)->shouldReturn(true);
    }
}
