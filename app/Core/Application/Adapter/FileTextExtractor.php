<?php

namespace App\Core\Application\Adapter;

interface FileTextExtractor
{
    public function extractFromFilePath(string $filePath): string;
}
