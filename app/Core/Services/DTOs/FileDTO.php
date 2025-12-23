<?php

namespace App\Core\DTOs;

readonly class FileDTO
{
    public function __construct(
        public string $name,
        public string $path,
    ) {}
}
