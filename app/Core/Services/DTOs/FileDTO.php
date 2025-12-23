<?php

namespace App\Core\Services\DTOs;

readonly class FileDTO
{
    public function __construct(
        public string $name,
        public string $path,
    ) {}
}
