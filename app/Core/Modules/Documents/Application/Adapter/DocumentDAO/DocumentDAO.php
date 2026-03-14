<?php

namespace App\Core\Modules\Documents\Application\Adapter\DocumentDAO;

use App\Core\Modules\Documents\Application\Adapter\DocumentDAO\DTOs\CreateDocumentDTO;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface DocumentDAO
{
    public function create(CreateDocumentDTO $createDocumentDTO): void;

    public function listByOwnerId(int $ownerId, int $perPage): LengthAwarePaginator;
}
