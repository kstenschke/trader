<?php

class CannotCloneSingletonException extends Exception
{
    /**
     * @param string    $message
     * @param int       $code
     * @param Exception $previous
     */
    public function __construct($message = 'Cannot clone a singleton', $code = 0, $previous = null)
    {
        Exception::__construct($message, $code, $previous);
    }
}
