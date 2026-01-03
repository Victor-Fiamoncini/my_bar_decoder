<?php

namespace App\Core\Data\Adapter;

interface FileTextExtractor
{
    public function extractFromFilePath(string $filePath): string;
}
