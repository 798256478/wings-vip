<?php

namespace App\Exceptions;

class WingException extends \Exception
{
    private $statusCode;

    /**
     * @param string  $message
     */
    public function __construct($message = 'An error occurred', $statusCode = null , $code=0, \Exception $previous = null)
    {
        $this->statusCode = $statusCode;

        parent::__construct($message, $code, $previous);
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
