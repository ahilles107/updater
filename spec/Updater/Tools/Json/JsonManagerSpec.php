<?php

namespace spec\Updater\Tools\Json;

use PhpSpec\ObjectBehavior;
use JsonSchema\Validator;

class JsonManagerSpec extends ObjectBehavior
{
    private $packagesDir;

    public function let()
    {
        $this->packagesDir = __DIR__.'/../../../packages/';
        $this->beConstructedWith(realpath($this->packagesDir.'update-4.3.1.zip'));
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Updater\Tools\Json\JsonManager');
    }

    public function it_gives_me_json_from_zip_file()
    {
        $this->getJsonFromFile('update.json', realpath($this->packagesDir.'update-4.3.1.zip'))->shouldBeString();
    }

    public function it_gives_valid_schema(Validator $validator)
    {
        $schema = file_get_contents(realpath(__DIR__.'/../../../../schema/').'/updater-schema.json');
        $json = $this->getJsonFromFile();
        $validator->isValid()->willReturn(true);
        $this->validateSchema($json->getWrappedObject(), $schema);
    }

    public function it_should_throw_error_when_invalid_json_file(Validator $validator)
    {
        $schema = file_get_contents(realpath(__DIR__.'/../../../../schema/').'/updater-schema.json');
        $validator->isValid()->willReturn(false);
        $this->shouldThrow('Updater\Tools\Json\JsonException')->during('validateSchema', array(
            'not valid json string',
            $schema,
        ));
    }

    public function it_adds_json_file_to_zip_archive()
    {
        $this->addJsonToFile('update.json', realpath($this->packagesDir.'update-4.3.1.zip'))->shouldReturn(true);
    }
}
