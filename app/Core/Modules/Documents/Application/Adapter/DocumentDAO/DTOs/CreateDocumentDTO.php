<?php

namespace App\Core\Modules\Documents\Application\Adapter\DocumentDAO\DTOs;

readonly class CreateDocumentDTO
{
    public function __construct(public string $name, public string $code, public int $userId) {}
}
