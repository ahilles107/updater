<?php

namespace spec\Updater\Tools\Files;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FilesManagerSpec extends ObjectBehavior
{
    private $packagesDir;

    function let()
    {
        $this->packagesDir = __DIR__ . '/../../../packages/';
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Updater\Tools\Files\FilesManager');
    }

    function it_loads_the_update_spec_from_a_zip_file()
    {
        $this->getSpecFromZip(realpath($this->packagesDir), 'update-4.3.1.zip')->shouldBeArray();
    }
}
