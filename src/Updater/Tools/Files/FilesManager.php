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
use Symfony\Component\Finder\Finder;

/**
 * Service to work with package files
 *
 * @author Paweł Mikołajczuk <mikolajczuk.private@gmail.com>
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 */
class FilesManager
{
    const DIFFFILENAME = 'upgrade-diff.json';

    /**
     * Get packages dir and file name to get update.json content in array
     *
     * @param string $packagesDir Directory with update packages
     * @param string $zipFileName Update package file name
     *
     * @return array Array witgh update packages definitions
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

    public function createJsonFileFromSchema($schemaPath, $reference, $targetPath)
    {
        $jsonManager = new JsonManager();
        $schema = file_get_contents($schemaPath);
        $validationResult = $jsonManager->validateJson($schema);

        if (true !== $validationResult) {
            throw new \Exception('JSON schema is not valid', 1);
        }

        $decodedSchema = json_decode($schema, true);
        $fileMapping = $this->findDiffFile($reference, $targetPath);
        $decodedSchema['filemapping'] = $fileMapping;

        $filePath = realpath($targetPath) . '/' . self::DIFFFILENAME;
        file_put_contents($filePath, json_encode($decodedSchema, defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : 0));
        $zipPath = realpath($targetPath . $reference . '.zip');

        if ($jsonManager->addJsonToFile($filePath, $zipPath)) {
            return true;
        }

        return false;
    }

    public function findDiffFile($reference, $targetPath)
    {
        $finder = new Finder();
        $finder->files()->name($reference . '.txt');

        $contents = null;
        foreach ($finder->in($targetPath) as $file) {
            $contents = $file->getContents();
        }

        return array_filter(preg_split('/\r\n|\n|\r/', $contents));
    }
}
