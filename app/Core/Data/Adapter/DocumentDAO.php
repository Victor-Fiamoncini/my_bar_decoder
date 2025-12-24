<?php

namespace App\Core\Data\Adapter;

interface DocumentDAO
{
    public function create(string $name, string $code, int $userId): void;
}
