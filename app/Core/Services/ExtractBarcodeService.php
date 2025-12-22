<?php

namespace App\Core\Services;

use App\Core\Services\Contracts\FileBarcodeExtractor;

class FileDto
{
    public function __construct(
        public readonly string $name,
        public readonly string $content,
    ) {}
}

class ExtractBarcodeService
{
    public function __construct(private readonly FileBarcodeExtractor $fileBarcodeExtractor) {}

    public function execute(FileDto $fileDto): ?string
    {
        $barcode = $this->fileBarcodeExtractor->extractFromFile($fileDto->content);

        return $barcode;
    }
}
