<?php


namespace App\Exceptions;

/**
 * The api client application can is allowed to see this Exception
 * Class CustomException
 * @package App\Exceptions
 */
class CustomException extends \Exception
{
    protected $customMessage;

    /**
     * this methode will be called to send the Excepyion method to the client application t help the client app developper
     * @return string
     */
    public function getCustomMessage()
    {
        return $this -> customMessage?$this->customMessage:$this->message;
    }

    /**
     * @param string $customMessage
     */
    public function setCustomMessage(string $customMessage): void
    {
        $this -> customMessage = $customMessage;
    }

}
