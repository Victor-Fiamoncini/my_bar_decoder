<?php

namespace App\Core\Data\Services\ExtractPaymentCode\Exceptions;

class ExtractPaymentCodeException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Failed to extract payment code from the provided file.');
    }
}
