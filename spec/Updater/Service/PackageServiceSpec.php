<?php

namespace spec\Updater\Service;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PackageServiceSpec extends ObjectBehavior
{
    private $packagesDir;

    function let()
    {
        $this->packagesDir = __DIR__ . '/../../packages/';
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Updater\Service\PackageService');
    }

    function it_fill_package_correctly()
    {
        $packageJson = array(json_decode(file_get_contents(realpath($this->packagesDir) . '/update-4.3.1.json'), true));
        $this->fillPackage($packageJson[0]);
    }
}
