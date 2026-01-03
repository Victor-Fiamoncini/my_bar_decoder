<?php

namespace App\Core\Infra;

use App\Core\Data\Adapter\DocumentDAO;
use App\Models\Document;

class EloquentDocumentDAO implements DocumentDAO
{
    public function create(string $name, string $code, int $userId): void
    {
        Document::create(['name' => $name, 'code' => $code, 'user_id' => $userId]);
    }
}
