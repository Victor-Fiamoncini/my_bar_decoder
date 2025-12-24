<?php

namespace App\Core\Data\Adapter;

interface FilePaymentCodeExtractor
{
    public function extractFromFilePath(string $filePath): string;
}
