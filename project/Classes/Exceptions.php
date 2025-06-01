<?php

class EmptyFetch extends Exception
{
    /**
     * Custom exception.
     * @param string $message [optional] The Exception message to throw.
     * @param int $code [optional] The Exception code.
     */
    public function __construct(string $message = '', int $code = 0) {
        parent::__construct($message, $code);
        $this->message = "$message";
        $this->code = $code;
    }
}