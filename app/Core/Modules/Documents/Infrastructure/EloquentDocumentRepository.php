<?php

namespace App\Core\Modules\Documents\Infrastructure;

use App\Core\Modules\Documents\Domain\Entities\Document\Document;
use App\Core\Modules\Documents\Domain\Repositories\DocumentRepository;
use App\Models\Document as EloquentDocument;

readonly class EloquentDocumentRepository implements DocumentRepository
{
    public function save(Document $document): void
    {
        EloquentDocument::create([
            'name' => $document->name,
            'code' => $document->paymentCode->code,
            'user_id' => $document->ownerId,
        ]);
    }
}
