<?php

namespace App\Core\Application\Adapter;

interface DocumentDAO
{
    public function create(string $name, string $code, int $userId): void;
}
