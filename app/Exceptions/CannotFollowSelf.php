<?php

namespace App\Exceptions;

use Exception;

class CannotFollowSelf extends Exception
{
    protected $message = "You cannot follow yourself";
}
