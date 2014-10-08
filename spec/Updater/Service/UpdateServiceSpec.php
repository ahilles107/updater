<?php

namespace spec\Updater\Service;

use PhpSpec\ObjectBehavior;
use Updater\Updater;
use Updater\Package\Package;
use Updater\Service\PackageService;

class UpdateServiceSpec extends ObjectBehavior
{
    private $packagesDir;

    public function let($die)
    {
        $updater = new Updater();
        $updater->setPackageService(new PackageService())
            ->setTempDir(realpath(__DIR__.'/../../sample_app/cache'))
            ->setWorkingDir(realpath(__DIR__.'/../../sample_app'));

        $this->beConstructedWith($updater);

        $packageService = $updater->getPackageService();
        $packageJson = array(json_decode(file_get_contents(__DIR__ . '/../../packages/update-4.3.1.json'), true));
        $package = $packageService->fillPackage($packageJson[0]);
        $package->setPackageDir(realpath(__DIR__ . '/../../../spec/packages').'/update-4.3.1.zip');
        $this->setPackage($package);
    }

    public function it_is_initializable(Updater $updater)
    {
        $this->shouldHaveType('Updater\Service\UpdateService');
    }

    public function it_copy_files_to_temp()
    {
        $this->copyFilesToTemp()->shouldReturn(true);
    }

    public function it_should_apply_file_changes()
    {
        $file = realpath(__DIR__ . '/../../../spec/packages').'/update-4.3.1.zip';
        $this->applyFileChanges($file)->shouldReturn(true);
    }

    public function it_should_rollback_file_changes()
    {
        $this->rollbackUpdate()->shouldReturn(true);
    }

    public function it_do_application_update()
    {
        $this->doUpdate($this->getPackage())->shouldReturn(true);
    }
}
