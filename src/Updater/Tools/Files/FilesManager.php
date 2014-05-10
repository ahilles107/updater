<?php

/*
 * This file is part of Updater.
 *
 * (c) Paweł Mikołajczuk <mikolajczuk.private@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Updater\Tools\Files;

use Updater\Tools\Json\JsonManager;

/**
 * Service to work with package files
 *
 * @author Paweł Mikołajczuk <mikolajczuk.private@gmail.com>
 */
class FilesManager
{
    /**
     * Get packages dir and file name to get update.json content in array
     *
     * @param string $packagesDir Directory with update packages
     * @param string $zipFileName Update package file name
     *
     * @return array              Array witgh update packages definitions
     */
    public function getSpecFromZip($packagesDir, $zipFileName = null)
    {
        $jsonManager = new JsonManager();
        $packages = array();

        foreach (new \RecursiveDirectoryIterator($packagesDir) as $file) {
            if (!$file->isFile()) {
                continue;
            }

            if (!extension_loaded('zip')) {
                throw new \Exception("You need to have zip extension enabled");
            }

            if ($zipFileName != null) {
                if ($file->getFilename() != $zipFileName) {
                    continue;
                }
            }

            $json = $jsonManager->getJsonFromFile('update.json', $file->getPathname());

            if ($json == false) {
                continue;
            }


            if (!$jsonManager->validateJson($json)) {
                continue;
            }

            $packages[] = json_decode($json, true);
        }

        return $packages;
    }
}
