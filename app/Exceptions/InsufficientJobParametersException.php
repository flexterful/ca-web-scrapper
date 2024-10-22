<?php

namespace App\Exceptions;

use Exception;

class InsufficientJobParametersException extends Exception
{
    protected $message = 'Insufficient Job Parameters';
}
