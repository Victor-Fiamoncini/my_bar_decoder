<?php

namespace App\Core\Modules\Documents\Application\Adapter;

interface FileTextExtractor
{
    public function extractFromFilePath(string $filePath): string;
}
