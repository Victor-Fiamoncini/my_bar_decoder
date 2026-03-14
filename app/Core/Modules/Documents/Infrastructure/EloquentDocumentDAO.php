<?php

namespace App\Core\Modules\Documents\Infrastructure;

use App\Core\Modules\Documents\Application\Adapter\DocumentDAO\DocumentDAO;
use App\Core\Modules\Documents\Application\Adapter\DocumentDAO\DTOs\CreateDocumentDTO;
use App\Models\Document;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

readonly class EloquentDocumentDAO implements DocumentDAO
{
    public function create(CreateDocumentDTO $createDocumentDTO): void
    {
        Document::create([
            'name' => $createDocumentDTO->name,
            'code' => $createDocumentDTO->code,
            'user_id' => $createDocumentDTO->userId,
        ]);
    }

    public function listByOwnerId(int $ownerId, int $perPage): LengthAwarePaginator
    {
        return Document::query()
            ->where('user_id', $ownerId)
            ->latest()
            ->paginate($perPage, ['id', 'name', 'code', 'created_at']);
    }
}
