<?php

namespace App\Core\Services\Contracts;

interface FileBarcodeExtractor
{
    public function extractFromFile(string $file): string;
}
