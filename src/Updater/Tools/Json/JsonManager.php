<?php

/*
 * This file is part of Updater.
 *
 * (c) Paweł Mikołajczuk <mikolajczuk.private@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Updater\Tools\Json;

use Seld\JsonLint\JsonParser;
use JsonSchema\Validator;

/**
 * Service to work with JSON
 *
 * @author Paweł Mikołajczuk <mikolajczuk.private@gmail.com>
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 */
class JsonManager
{
    public function getJsonFromFile($fileName, $pathName)
    {
        $zip = new \ZipArchive();
        $zip->open($pathName);

        if (0 == $zip->numFiles) {
            return false;
        }

        $foundFileIndex = $zip->locateName($fileName, \ZipArchive::FL_NODIR);
        if (false === $foundFileIndex) {
            return false;
        }

        $updateFileName = $zip->getNameIndex($foundFileIndex);

        $updateFile = "zip://{$pathName}#$updateFileName";
        $json = file_get_contents($updateFile);

        return $json;
    }

    public function validateJson($json)
    {
        $parser = new JsonParser();

        $lintResult = $parser->lint($json);
        if (null === $lintResult) {
            return true;
        }

        return $lintResult;
    }

    public function validateSchema($json, $schema)
    {
        $validator = new Validator();
        $validator->check(json_decode($json), json_decode($schema));

        if ($validator->isValid()) {
            return true;
        } else {
            return $validator->getErrors();
        }
    }

    /**
     * Adds upgrade json file to zip package as upgrade-diff.json
     *
     * @param string $filePath Path to json file that will be added to archive
     * @param string $zipPath  Zip file path, to which json file will be added
     *
     * @return boolean
     */
    public function addJsonToFile($filePath, $zipPath)
    {
        $zip = new \ZipArchive();
        $zip->open($zipPath);

        if (0 == $zip->numFiles) {
            return false;
        }

        $zip->addFile($filePath, 'upgrade-diff.json');
        $zip->close();

        return true;
    }
}
