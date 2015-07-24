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
use Updater\Tools\Files\FilesManager;

/**
 * Service to work with JSON.
 *
 * @author Paweł Mikołajczuk <mikolajczuk.private@gmail.com>
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 */
class JsonManager
{
    protected $filePath;

    /**
     * Construct.
     *
     * @param string $filePath Package file path.
     */
    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Gets the package file path.
     *
     * @return string real path
     */
    public function getFilePath()
    {
        return realpath($this->filePath);
    }

    /**
     * Gets the JSON file name. This file is
     * located in the ppackage file.
     *
     * @return string JSON file name
     */
    public function getFileName()
    {
        return FilesManager::DIFF_FILENAME;
    }

    /**
     * Gets the JSON file content from the package.
     *
     * @param string $jsonFileName JSON file name
     *
     * @return string JSON string
     */
    public function getJsonFromFile($jsonFileName = null)
    {
        $zip = new \ZipArchive();
        $zip->open($this->getFilePath());

        if (0 == $zip->numFiles) {
            return false;
        }

        $foundFileIndex = $zip->locateName(
            is_null($jsonFileName) ? $this->getFileName() : $jsonFileName,
            \ZipArchive::FL_NODIR
        );
        if (false === $foundFileIndex) {
            return false;
        }

        $updateFileName = $zip->getNameIndex($foundFileIndex);

        $updateFile = "zip://{$this->getFilePath()}#$updateFileName";
        $json = file_get_contents($updateFile);

        if (!is_string($json)) {
            throw new \Exception(sprintf(
                'Could not fetch content from "%s" file inside "%s"',
                $this->getFilename(),
                $this->getFilePath()
            ));
        }

        return $json;
    }

    /**
     * Validates the JSON string if it matches
     * the expected JSON schema.
     *
     * @param string $json
     * @param string $schema
     *
     * @return bool true on success
     *
     * @throws JsonException
     */
    public function validateSchema($json, $schema)
    {
        $validator = new Validator();
        $this->validateJson($json);
        $validator->check(json_decode($json), json_decode($schema));
        if (!$validator->isValid()) {
            $errors = array();
            foreach ((array) $validator->getErrors() as $error) {
                $errors[] = ($error['property'] ? $error['property'].' : ' : '').$error['message'];
            }

            throw new JsonException(
                sprintf(
                    '"%s" file inside "%s" does not match the expected JSON schema.',
                    $this->getFilename(),
                    $this->getFilePath()
                ),
                $errors
            );
        }

        return true;
    }

    /**
     * Validates the syntax of a JSON string.
     *
     * @param string $json
     *
     * @return bool true on success
     *
     * @throws JsonException
     */
    public function validateJson($json)
    {
        $parser = new JsonParser();
        $lintResult = $parser->lint($json);
        if (null === $lintResult) {
            return true;
        }

        $errors = array();
        $errors[] = $lintResult->getMessage();
        throw new JsonException(
            sprintf(
                'Syntax of a "%s" file inside "%s" does not validate.',
                $this->getFileName(),
                $this->getFilePath()
            ),
            $errors
        );
    }

    /**
     * Adds upgrade json file to zip package as update.json.
     *
     * @param string $filePath Path to json file that will be added to archive
     * @param string $zipPath  Zip file path, to which json file will be added
     *
     * @return bool
     */
    public function addJsonToFile($filePath, $zipPath)
    {
        if (!extension_loaded('zip')) {
            throw new \Exception('You need to have zip extension enabled');
        }

        $zip = new \ZipArchive();
        $zip->open($zipPath);

        if (0 == $zip->numFiles) {
            return false;
        }

        $zip->addFile($filePath, $this->getFileName());
        $zip->close();

        return true;
    }
}
