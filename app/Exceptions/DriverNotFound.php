<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class DriverNotFound extends HttpException
{
    public function __construct()
    {
        parent::__construct(400, 'The specified driver name is not supported.');
    }
}
