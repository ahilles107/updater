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
 * Updater
 *
 * @author Paweł Mikołajczuk <mikolajczuk.private@gmail.com>
 */
class Updater
{
    /**
     * Get packages dir and file name to get update.json content in array
     *
     * @param string $packagesDir Directory with update packages
     * @param [type] $zipFileName Update package file name
     *
     * @return array              Array witgh update packages definitions
     */
    public function getSpecFromZip($packagesDir, $zipFileName = null)
    {
        $packages = array();

        foreach (new \RecursiveDirectoryIterator($packagesDir) as $file) {
            if (!$file->isFile()) {
                continue;
            }

            if (!extension_loaded('zip')) {
                throw new Exception("In order to use private plugins, you need to have zip extension enabled");
            }

            if ($zipFileName != null) {
                if ($file->getFilename() != $zipFileName) {
                    continue;
                }
            }

            $zip = new \ZipArchive();
            $zip->open($file->getPathname());

            if (0 == $zip->numFiles) {
                continue;
            }

            $foundFileIndex = $zip->locateName('update.json', \ZipArchive::FL_NODIR);
            if (false === $foundFileIndex) {
                continue;
            }

            $configurationFileName = $zip->getNameIndex($foundFileIndex);

            $composerFile = "zip://{$file->getPathname()}#$configurationFileName";
            $json = file_get_contents($composerFile);

            $package = json_decode($json, true);
            $packages[] = $package;
        }

        return $packages;
    }
}
