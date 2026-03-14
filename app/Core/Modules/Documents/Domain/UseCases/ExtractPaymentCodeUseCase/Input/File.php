<?php

namespace App\Core\Modules\Documents\Domain\UseCases\ExtractPaymentCodeUseCase\Input;

readonly class File
{
    public function __construct(public string $name, public string $path) {}
}
