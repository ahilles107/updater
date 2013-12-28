<?php

namespace spec\Updater;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class UpdaterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Updater\Updater');
    }

    function it_get_update_spec_from_zip_file()
    {
        $package_spec = array(json_decode(file_get_contents(realpath(__DIR__ . '/../packages/') . '/update-4.3.1.json'), true));
        $this->getSpecFromZip(realpath(__DIR__ . '/../packages/'), 'update-4.3.1.zip')->shouldBeLike($package_spec);
    }
}
