<?php

/*
 * This file is part of Updater.
 *
 * (c) Paweł Mikołajczuk <mikolajczuk.private@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Updater\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Updater\Updater;
use Updater\Package\Package;

class UpdateService
{
    private $updater;

    private $package;

    // apply package
    // * validate package
    // * create copy of upgraded files
    // * remove files to remove
    // * update files to upgrade
    // * run composer action

    public function __construct(Updater $updater)
    {
        $this->updater = $updater;
    }

    public function doUpdate(Package $package)
    {
        $this->setPackage($package);
        $this->copyFilesToTemp();

        try {
            $this->applyFileChanges();
        } catch (\Exception $e) {
            $this->rollbackUpdate($package);
        }
    }

    public function rollbackUpdate()
    {
        $fileMapping = $this->package->getFilemapping();
        $fs = new Filesystem();

        foreach ($fileMapping as $file) {
            if ($fs->exists($this->updater->getTempDir().'/oldfiles/'.$file['file']) && $file['type'] == 'update') {
                $fs->copy($this->updater->getTempDir().'/oldfiles/'.$file['file'], $this->updater->getWorkingDir().'/'.$file['file']);
            } elseif ($fs->exists($this->updater->getWorkingDir().'/'.$file['file']) && $file['type'] == 'add') {
                $fs->remove($this->updater->getWorkingDir().'/'.$file['file']);
            } elseif ($fs->exists($this->updater->getTempDir().'/oldfiles/'.$file['file']) && $file['type'] == 'remove') {
                $fs->copy($this->updater->getTempDir().'/oldfiles/'.$file['file'], $this->updater->getWorkingDir().'/'.$file['file']);
            }
        }

        return true;
    }

    public function copyFilesToTemp()
    {
        $fileMapping = $this->package->getFilemapping();
        $fs = new Filesystem();

        $fs->mkdir($this->updater->getTempDir().'/oldfiles/');
        foreach ($fileMapping as $file) {
            if ($fs->exists($this->updater->getWorkingDir().'/'.$file['file']) && $file['type'] != 'add') {
                $fs->copy($this->updater->getWorkingDir().'/'.$file['file'], $this->updater->getTempDir().'/oldfiles/'.$file['file']);
            } elseif (!$fs->exists($this->updater->getWorkingDir().'/'.$file['file']) && $file['type'] == 'update') {
                throw new \Exception($this->updater->getWorkingDir().'/'.$file['file'] . " don't exists");
            }
        }

        return true;
    }

    public function applyFileChanges($updatePackage)
    {
        $this->extractUpdatePackageToTemp($updatePackage);

        $fileMapping = $this->package->getFilemapping();
        $fs = new Filesystem();

        foreach ($fileMapping as $file) {
            if ($fs->exists($this->updater->getTempDir().'/newfiles/'.$file['file']) && $file['type'] == 'update') {
                $fs->copy($this->updater->getTempDir().'/newfiles/'.$file['file'], $this->updater->getWorkingDir().'/'.$file['file']);
            } elseif ($fs->exists($this->updater->getTempDir().'/newfiles/'.$file['file']) && $file['type'] == 'add') {
                $fs->copy($this->updater->getTempDir().'/newfiles/'.$file['file'], $this->updater->getWorkingDir().'/'.$file['file']);
            } elseif ($fs->exists($this->updater->getWorkingDir().'/'.$file['file']) && $file['type'] == 'remove') {
                $fs->remove($this->updater->getWorkingDir().'/'.$file['file']);
            }
        }

        return true;
    }

    public function setPackage(Package $package)
    {
        $this->package = $package;

        return $this;
    }

    private function getFileContentFromZip($zipFilePath, $fileName)
    {
        if (!extension_loaded('zip')) {
            throw new \Exception("You need to have zip extension enabled");
        }

        $zip = new \ZipArchive();
        $zip->open($zipFilePath);

        if (0 == $zip->numFiles) {
            return false;
        }

        $file = "zip://{$zipFilePath}#$fileName";
        $content = file_get_contents($file);

        return $content;
    }

    private function extractUpdatePackageToTemp($zipFile)
    {
        if (!extension_loaded('zip')) {
            throw new \Exception("You need to have zip extension enabled");
        }

        $fs = new Filesystem();
        $fs->mkdir($this->updater->getTempDir().'/newfiles/');

        $zip = new \ZipArchive;
        if ($zip->open($zipFile) === true) {
            $zip->extractTo($this->updater->getTempDir().'/newfiles/');
            $zip->close();
        } else {
            throw new \Exception("Update file was not found!");
        }
    }
}
