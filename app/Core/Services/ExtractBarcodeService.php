<?php

namespace App\Core\Services;

use App\Core\DTOs\FileDTO;
use App\Core\Services\Contracts\FileBarcodeExtractor;

readonly class ExtractBarcodeService
{
    public function __construct(private FileBarcodeExtractor $fileBarcodeExtractor) {}

    public function execute(FileDTO $fileDTO): ?string
    {
        $paymentCode = $this->fileBarcodeExtractor->extractFromFilePath($fileDTO->path);

        return $paymentCode;
    }
}
