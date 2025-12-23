<?php

namespace App\Core\Services\Contracts;

interface FileBarcodeExtractor
{
    public function extractFromFilePath(string $filePath): string;
}
