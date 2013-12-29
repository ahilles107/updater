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

/**
 * Update informations represented as a class
 *
 * @author Paweł Mikołajczuk <mikolajczuk.private@gmail.com>
 */
class Package
{
    /**
     * Version name
     * @var string
     */
    private $version;

    /**
     * Short description for package
     * @var string
     */
    private $description;

    /**
     * Type of update
     * @var string
     */
    private $updateType;

    /**
     * Chnagelog
     * @var array
     */
    private $changelog;

    /**
     * Name of package mainatainer
     * @var string
     */
    private $mainatiner;

    /**
     * Array with files mapping
     * @var array
     */
    private $filemapping;

    /**
     * Path for mapped files inside zip file
     * @var string
     */
    private $filesDir;

    /**
     * Path for directory with migrations
     * @var string
     */
    private $migrationsDir;

    /**
     * Composer action
     * @var string
     */
    private $composerAction;

    /**
     * Gets the Version name.
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Sets the Version name.
     *
     * @param string $version the version
     *
     * @return self
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Gets the Short description for package.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the Short description for package.
     *
     * @param string $description the description
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Gets the Type of update.
     *
     * @return string
     */
    public function getUpdateType()
    {
        return $this->updateType;
    }

    /**
     * Sets the Type of update.
     *
     * @param string $updateType the update type
     *
     * @return self
     */
    public function setUpdateType($updateType)
    {
        $this->updateType = $updateType;

        return $this;
    }

    /**
     * Gets the Chnagelog.
     *
     * @return array
     */
    public function getChangelog()
    {
        return $this->changelog;
    }

    /**
     * Sets the Chnagelog.
     *
     * @param array $changelog the changelog
     *
     * @return self
     */
    public function setChangelog(array $changelog)
    {
        $this->changelog = $changelog;

        return $this;
    }

    /**
     * Gets the Name of package mainatainer.
     *
     * @return string
     */
    public function getMainatiner()
    {
        return $this->mainatiner;
    }

    /**
     * Sets the Name of package mainatainer.
     *
     * @param string $mainatiner the mainatiner
     *
     * @return self
     */
    public function setMainatiner($mainatiner)
    {
        $this->mainatiner = $mainatiner;

        return $this;
    }

    /**
     * Gets the Array with files mapping.
     *
     * @return array
     */
    public function getFilemapping()
    {
        return $this->filemapping;
    }

    /**
     * Sets the Array with files mapping.
     *
     * @param array $filemapping the filemapping
     *
     * @return self
     */
    public function setFilemapping(array $filemapping)
    {
        $this->filemapping = $filemapping;

        return $this;
    }

    /**
     * Gets the Path for mapped files inside zip file.
     *
     * @return string
     */
    public function getFilesDir()
    {
        return $this->filesDir;
    }

    /**
     * Sets the Path for mapped files inside zip file.
     *
     * @param string $filesDir the files dir
     *
     * @return self
     */
    public function setFilesDir($filesDir)
    {
        $this->filesDir = $filesDir;

        return $this;
    }

    /**
     * Gets the Path for directory with migrations.
     *
     * @return string
     */
    public function getMigrationsDir()
    {
        return $this->migrationsDir;
    }

    /**
     * Sets the Path for directory with migrations.
     *
     * @param string $migrationsDir the migrations dir
     *
     * @return self
     */
    public function setMigrationsDir($migrationsDir)
    {
        $this->migrationsDir = $migrationsDir;

        return $this;
    }

    /**
     * Gets the Composer action.
     *
     * @return string
     */
    public function getComposerAction()
    {
        return $this->composerAction;
    }

    /**
     * Sets the Composer action.
     *
     * @param string $composerAction the composer action
     *
     * @return self
     */
    public function setComposerAction($composerAction)
    {
        $this->composerAction = $composerAction;

        return $this;
    }
}
