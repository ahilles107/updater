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
use Symfony\Component\Filesystem\Filesystem;

/**
 * Service to work with package files
 *
 * @author Paweł Mikołajczuk <mikolajczuk.private@gmail.com>
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 */
class FilesManager
{
    /**
     * File with diffrences between commits name
     */
    const DIFFFILENAME = 'update.json';

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

    /**
     * Creates JSON file from schema
     *
     * @param string $schemaPath Schema path
     * @param array  $arguments  Array of arguments
     *
     * @return boolean
     */
    public function createJsonFileFromSchema($schemaPath, $arguments)
    {
        $jsonManager = new JsonManager();
        $fs = new Filesystem();
        $schema = file_get_contents($schemaPath);
        $validationResult = $jsonManager->validateJson($schema);

        if (true !== $validationResult) {
            throw new \Exception('JSON schema is not valid', 1);
        }

        $decodedSchema = json_decode($schema, true);
        $fileMapping = $this->getFileContent($arguments['version'] . '.txt', $arguments['target']);

        $decodedSchema['changelog'] = $this->getFileContent($arguments['version'] . '_commits.txt', $arguments['target']);
        $decodedSchema['filemapping'] = $fileMapping;

        foreach ($decodedSchema as $key => $value) {
            if (array_key_exists($key, $arguments)) {
                $decodedSchema[$key] = $arguments[$key];
            }
        }

        if (isset($arguments['include']) && isset($arguments['comparePath'])) {
            $decodedSchema['include'] = preg_replace('#/+#', '/', $arguments['comparePath'] . '/'. $arguments['include']);
        }

        $filePath = realpath($arguments['target']) . '/' . self::DIFFFILENAME;
        file_put_contents($filePath, json_encode($decodedSchema, defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : 0), LOCK_EX);
        $zipPath = realpath($arguments['target'] . $arguments['version'] . '.zip');
        if ($jsonManager->addJsonToFile($filePath, $zipPath)) {
            $fs->remove(array($filePath));

            return true;
        }

        return false;
    }

    /**
     * Finds file by given name and given directory then converts it to array
     *
     * @param string $reference  Commit or TAG
     * @param string $targetPath Target path where txt file is located
     *
     * @return array Each line is array value
     */
    public function getFileContent($reference, $targetPath)
    {
        $finder = new Finder();
        $finder->files()->name($reference);

        $contents = null;
        foreach ($finder->in($targetPath) as $file) {
            $contents = $file->getContents();
        }

        return array_filter(preg_split('/\r\n|\n|\r/', $contents));
    }

    /**
     * Exclude files or directories from update package
     *
     * @param array $fileMapping Array from which files or dirs will be exluded
     * @param array $excludes    Array with values to exclude
     *
     * @return array Array with excludes dirs and files
     */
    public function exclude(array $fileMapping, array $excludes)
    {
        $result = array();
        foreach ($excludes as $value) {
            $result = array_filter($fileMapping, function ($element) use ($value) {
                return (strpos($element, $value) === false);
            });

            $fileMapping = $result;
        }

        return $fileMapping;
    }
}
