<?php

namespace App\Core\Services;

use App\Core\Services\Contracts\FileBarcodeExtractor;
use App\Core\Services\DTOs\FileDTO;
use App\Core\Services\Exceptions\ExtractPaymentCodeException;
use App\Models\Document;

readonly class ExtractBarcodeService
{
    public function __construct(private FileBarcodeExtractor $fileBarcodeExtractor) {}

    /**
     * @throws ExtractPaymentCodeException
     */
    public function execute(FileDTO $fileDTO): string
    {
        $paymentCode = $this->fileBarcodeExtractor->extractFromFilePath($fileDTO->path);

        if ($paymentCode) {
            Document::create([
                'name' => $fileDTO->name,
                'code' => $paymentCode,
                'user_id' => auth()->id(),
            ]);

            return $paymentCode;
        }

        throw new ExtractPaymentCodeException;
    }
}
