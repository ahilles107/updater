<?php

namespace spec\Updater\Service;

use PhpSpec\ObjectBehavior;

class PackageServiceSpec extends ObjectBehavior
{
    private $packagesDir;

    public function let()
    {
        $this->packagesDir = __DIR__ . '/../../packages/';
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Updater\Service\PackageService');
    }

    public function it_fill_package_correctly()
    {
        $packageJson = array(json_decode(file_get_contents(realpath($this->packagesDir) . '/update-4.3.1.json'), true));
        $this->fillPackage($packageJson[0]);
    }
}
