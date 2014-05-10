<?php

/*
 * This file is part of Updater.
 *
 * (c) Paweł Mikołajczuk <mikolajczuk.private@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Updater;

use Updater\Service\PackageService;

/**
 * Updater
 *
 * @author Paweł Mikołajczuk <mikolajczuk.private@gmail.com>
 */
class Updater
{
    private $jsonTools;

    private $filesTools;

    private $databaseTools;

    /**
     * Package service
     * @var PackageService
     */
    private $packageService;

    /**
     * Update temp/cache directory
     * @var string
     */
    private $tempDir;

    /**
     * Working project root directory
     * @var string
     */
    private $workingDir;

    /**
     * Gets the Update temp/cache directory.
     *
     * @return string
     */
    public function getTempDir()
    {
        return $this->tempDir;
    }

    /**
     * Sets the Update temp/cache directory.
     *
     * @param string $tempDir the temp dir
     *
     * @return self
     */
    public function setTempDir($tempDir)
    {
        $this->tempDir = $tempDir;

        return $this;
    }

    /**
     * Gets the Package service.
     *
     * @return PackageService
     */
    public function getPackageService()
    {
        return $this->packageService;
    }

    /**
     * Sets the Package service.
     *
     * @param PackageService $packageService the package service
     *
     * @return self
     */
    public function setPackageService(PackageService $packageService)
    {
        $this->packageService = $packageService;

        return $this;
    }

    /**
     * Gets the Working project root directory.
     *
     * @return string
     */
    public function getWorkingDir()
    {
        return $this->workingDir;
    }

    /**
     * Sets the Working project root directory.
     *
     * @param string $workingDir the working dir
     *
     * @return self
     */
    public function setWorkingDir($workingDir)
    {
        $this->workingDir = $workingDir;

        return $this;
    }
}
