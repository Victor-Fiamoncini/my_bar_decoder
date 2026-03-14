<?php

namespace App\Core\Modules\Documents\Domain\Repositories;

use App\Core\Modules\Documents\Domain\Entities\Document\Document;

interface DocumentRepository
{
    public function save(Document $document): void;
}
