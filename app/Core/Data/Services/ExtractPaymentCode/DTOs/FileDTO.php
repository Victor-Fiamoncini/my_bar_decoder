<?php

namespace App\Core\Data\Services\ExtractPaymentCode\DTOs;

readonly class FileDTO
{
    public function __construct(
        public string $name,
        public string $path,
    ) {}
}
