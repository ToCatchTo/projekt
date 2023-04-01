<?php

class BaseException extends Exception
{
    #[\JetBrains\PhpStorm\Pure]public function __construct(string $message = "Server error", int $code = 500, ?Throwable $previous = null) {
        parent::__construct($message,$code,$previous);
    }
}