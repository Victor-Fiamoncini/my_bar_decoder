<?php

namespace App\Core\Modules\Documents\Infrastructure;

use App\Core\Modules\Documents\Application\Adapter\DocumentDAO\DocumentDAO;
use App\Models\Document;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

readonly class EloquentDocumentDAO implements DocumentDAO
{
    public function listByOwnerId(int $ownerId, int $perPage): LengthAwarePaginator
    {
        return Document::query()
            ->where('user_id', $ownerId)
            ->latest()
            ->paginate($perPage, ['id', 'name', 'code', 'created_at']);
    }
}
