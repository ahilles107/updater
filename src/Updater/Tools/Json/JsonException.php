<?php

namespace Updater\Tools\Json;

use Exception;

class JsonException extends Exception
{
    protected $errors;

    public function __construct($message, $errors = array(), Exception $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
