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

/**
 * Service to work with JSON
 *
 * @author Paweł Mikołajczuk <mikolajczuk.private@gmail.com>
 */
class JsonManager
{
    public static function getJsonFromFile($fileName, $pathName)
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

    public static function validateJson($json)
    {
        $parser = new JsonParser();

        return null === $parser->lint($json);
    }
}
