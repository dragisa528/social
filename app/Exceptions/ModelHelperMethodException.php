<?php

namespace App\Exceptions;

use Exception;

class ModelHelperMethodException extends Exception
{
    protected $message = 'Model helper methods cannot be called from model without an ID';
}
