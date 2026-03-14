<?php

namespace App\Core\Modules\Documents\Application\Adapter\DocumentDAO;

use App\Core\Modules\Documents\Application\Adapter\DocumentDAO\DTOs\CreateDocumentDTO;

interface DocumentDAO
{
    public function create(CreateDocumentDTO $createDocumentDTO): void;
}
