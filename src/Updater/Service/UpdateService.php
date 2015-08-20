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
use Updater\Tools\Files\FilesManager;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class UpdateService
{
    const TYPE_UPDATE = 'update';
    const TYPE_REMOVE = 'remove';
    const TYPE_ADD = 'add';

    private $updater;

    private $package;

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
        $fileMapping = $this->includeConfigDir($this->package->getInclude());
        if (empty($fileMapping)) {
            $fileMapping = $this->package->getFilemapping();
        }

        foreach ((array) $fileMapping as $file) {
            $this->copyFiles($file, true);
        }

        return true;
    }

    private function copyFiles(array $file, $rollback = false)
    {
        $fileSystem = new Filesystem();
        $dir = FilesManager::NEW_FILES_DIR;
        if ($rollback) {
            $dir = FilesManager::OLD_FILES_DIR;
        }

        $filePath = $this->updater->getTempDir().$dir.$file['file'];

        $file['path'] = $filePath;
        if ($fileSystem->exists($filePath)) {
            switch ($file['type']) {
                case self::TYPE_UPDATE || self::TYPE_REMOVE:
                    $this->copyFileFromTempToWorkingDir($file, true);

                    break;
                case self::TYPE_ADD:
                    $fileSystem->remove($this->updater->getWorkingDir().$file['file']);

                    break;
                default:
                    break;
            }
        }
    }

    private function copyFileFromTempToWorkingDir(array $file, $override = false)
    {
        $fileSystem = new Filesystem();
        if (is_link($this->updater->getTempDir().$file['file'])) {
            $fileSystem->symlink(
                $file['path'],
                $this->updater->getWorkingDir().$file['file'],
                true
            );
        } else {
            $fileSystem->copy(
                $file['path'],
                $this->updater->getWorkingDir().$file['file'],
                $override
            );
        }
    }

    public function copyFilesToTemp()
    {
        $fileSystem = new Filesystem();
        $fileMapping = $this->includeConfigDir($this->package->getInclude());
        if (empty($fileMapping)) {
            $fileMapping = $this->package->getFilemapping();
        }

        $this->package->setRealFileMapping($fileMapping);
        $fileSystem->mkdir($this->updater->getTempDir().FilesManager::OLD_FILES_DIR);
        foreach ((array) $fileMapping as $file) {
            $exists = $fileSystem->exists($this->updater->getWorkingDir().$file['file']);
            if ($exists && $file['type'] !== self::TYPE_ADD) {
                $this->copyFileFromWorkingDirToTemp($file);
            } elseif (!$exists && $file['type'] === self::TYPE_UPDATE) {
                throw new FileNotFoundException($this->updater->getWorkingDir().$file['file']." doesn't exist.");
            }
        }

        return true;
    }

    private function copyFileFromWorkingDirToTemp($file, $override = false)
    {
        $fileSystem = new Filesystem();
        $filePath = $this->updater->getTempDir().FilesManager::OLD_FILES_DIR.$file['file'];
        if (is_link($this->updater->getWorkingDir().$file['file'])) {
            $fileSystem->symlink(
                $this->updater->getWorkingDir().$file['file'],
                $filePath,
                true
            );
        } else {
            $fileSystem->copy(
                $this->updater->getWorkingDir().$file['file'],
                $filePath,
                $override
            );
        }
    }

    public function includeConfigDir($configDir)
    {
        $fileMapping = $this->package->getFilemapping();
        $finder = new Finder();
        if (!is_null($configDir) && !empty($configDir)) {
            $workingDirConfig = realpath($this->updater->getWorkingDir().$configDir);
            $configFiles = array();
            if (!is_dir($workingDirConfig)) {
                throw new \Exception('Directory: '.$this->updater->getWorkingDir().$configDir." doesn't exist.");
            }

            foreach ($finder->in(realpath($workingDirConfig)) as $item) {
                $configFiles[] = array(
                    'file' => str_replace($this->updater->getWorkingDir(), '', $item->getRealPath()),
                    'type' => 'update',
                );
            }

            if (!empty($fileMapping)) {
                foreach ((array) $fileMapping as $file) {
                    foreach ($configFiles as $key => $value) {
                        if (strpos($file['file'], $value['file']) !== false) {
                            unset($configFiles[$key]);
                        }
                    }
                }

                return array_merge($fileMapping, $configFiles);
            }
        }

        return array();
    }

    public function applyFileChanges($updatePackage)
    {
        $this->extractUpdatePackageToTemp($updatePackage);
        $fileMapping = $this->package->getFilemapping();
        foreach ($fileMapping as $file) {
            $this->copyFiles($file);
        }

        return true;
    }

    public function setPackage(Package $package)
    {
        $this->package = $package;

        return $this;
    }

    private function extractUpdatePackageToTemp($zipFile)
    {
        if (!extension_loaded('zip')) {
            throw new \Exception('You need to have zip extension enabled');
        }

        $fileSystem = new Filesystem();
        $fileSystem->mkdir($this->updater->getTempDir().FilesManager::NEW_FILES_DIR);

        $zip = new \ZipArchive();
        if ($zip->open($zipFile) === true) {
            $zip->extractTo($this->updater->getTempDir().FilesManager::NEW_FILES_DIR);
            $zip->close();
        } else {
            throw new \Exception('Update file was not found!');
        }
    }

    public function getPackage()
    {
        return $this->package;
    }
}
