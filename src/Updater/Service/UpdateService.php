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
use Updater\Updater;
use Updater\Package\Package;
use Symfony\Component\Finder\Finder;

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

    public function doUpdate()
    {
        $this->copyFilesToTemp();

        try {
            $this->applyFileChanges($this->package->getPackageDir());

            return true;
        } catch (\Exception $e) {
            $this->rollbackUpdate();

            return false;
        }
    }

    public function rollbackUpdate()
    {
        $fs = new Filesystem();
        $fileMapping = $this->includeConfigDir($this->package->getInclude());
        if (empty($fileMapping)) {
            $fileMapping = $this->package->getFilemapping();
        }

        foreach ($fileMapping as $file) {
            if ($fs->exists($this->updater->getTempDir().'/oldfiles/'.$file['file']) && $file['type'] == 'update') {
                if (is_link($this->updater->getTempDir().'/oldfiles/'.$file['file'])) {
                    $fs->symlink($this->updater->getTempDir().'/oldfiles/'.$file['file'], $this->updater->getWorkingDir().'/'.$file['file'], true);
                } else {
                    $fs->copy($this->updater->getTempDir().'/oldfiles/'.$file['file'], $this->updater->getWorkingDir().'/'.$file['file'], true);
                }
            } elseif ($fs->exists($this->updater->getWorkingDir().'/'.$file['file']) && $file['type'] == 'add') {
                $fs->remove($this->updater->getWorkingDir().'/'.$file['file']);
            } elseif ($fs->exists($this->updater->getTempDir().'/oldfiles/'.$file['file']) && $file['type'] == 'remove') {
                if (is_link($this->updater->getTempDir().'/oldfiles/'.$file['file'])) {
                    $fs->symlink($this->updater->getTempDir().'/oldfiles/'.$file['file'], $this->updater->getWorkingDir().'/'.$file['file'], true);
                } else {
                    $fs->copy($this->updater->getTempDir().'/oldfiles/'.$file['file'], $this->updater->getWorkingDir().'/'.$file['file'], true);
                }
            }
        }

        return true;
    }

    public function copyFilesToTemp()
    {
        $fs = new Filesystem();
        $fileMapping = $this->includeConfigDir($this->package->getInclude());
        if (empty($fileMapping)) {
            $fileMapping = $this->package->getFilemapping();
        }

        $this->package->setRealFileMapping($fileMapping);
        $fs->mkdir($this->updater->getTempDir().'/oldfiles/');
        foreach ($fileMapping as $file) {
            $exists = $fs->exists($this->updater->getWorkingDir().'/'.$file['file']);

            if ($exists && $file['type'] != 'add') {
                if (is_link($this->updater->getWorkingDir().'/'.$file['file'])) {
                    $fs->symlink($this->updater->getWorkingDir().'/'.$file['file'], $this->updater->getTempDir().'/oldfiles/'.$file['file'], true);
                } else {
                    $fs->copy($this->updater->getWorkingDir().'/'.$file['file'], $this->updater->getTempDir().'/oldfiles/'.$file['file']);
                }
            } elseif (!$exists && $file['type'] == 'update') {
                throw new \Exception($this->updater->getWorkingDir().'/'.$file['file'] . " doesn't exist.");
            }
        }

        return true;
    }

    public function includeConfigDir($configDir)
    {
        $fileMapping = $this->package->getFilemapping();
        $finder = new Finder();
        $fs = new Filesystem();
        if (!is_null($configDir) && !empty($configDir)) {
            $workingDirConfig = realpath($this->updater->getWorkingDir().'/'.$configDir);
            $configFiles = array();
            if (!is_dir($workingDirConfig)) {
                throw new \Exception("Directory: " . $this->updater->getWorkingDir().'/'.$configDir . " doesn't exist.");
            }

            foreach ($finder->in(realpath($workingDirConfig)) as $item) {
                $configFiles[] = array(
                    'file' => str_replace($this->updater->getWorkingDir().'/', '', $item->getRealPath()),
                    'type' => 'update'
                );
            }

            foreach ($fileMapping as $file) {
                foreach ($configFiles as $key => $value) {
                    if (strpos($file['file'], $value['file']) !== false) {
                        unset($configFiles[$key]);
                    }
                }
            }

            return array_merge($fileMapping, $configFiles);
        }

        return array();
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

    public function getPackage()
    {
        return $this->package;
    }
}
