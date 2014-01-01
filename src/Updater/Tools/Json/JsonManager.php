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
}
