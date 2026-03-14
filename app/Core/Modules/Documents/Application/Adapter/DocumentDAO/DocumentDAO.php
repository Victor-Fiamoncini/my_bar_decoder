<?php

namespace App\Core\Modules\Documents\Application\Adapter\DocumentDAO;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface DocumentDAO
{
    public function listByOwnerId(int $ownerId, int $perPage): LengthAwarePaginator;
}
