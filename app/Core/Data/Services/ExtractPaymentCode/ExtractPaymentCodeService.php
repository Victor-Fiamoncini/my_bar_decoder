<?php

namespace App\Core\Data\Services\ExtractPaymentCode;

use App\Core\Data\Adapter\DocumentDAO;
use App\Core\Data\Adapter\FilePaymentCodeExtractor;
use App\Core\Data\Services\ExtractPaymentCode\DTOs\FileDTO;
use App\Core\Data\Services\ExtractPaymentCode\Exceptions\ExtractPaymentCodeException;

class ExtractPaymentCodeService
{
    public function __construct(private readonly FilePaymentCodeExtractor $filePaymentCodeExtractor, private readonly DocumentDAO $documentDAO) {}

    /**
     * @throws ExtractPaymentCodeException
     */
    public function execute(int $documentOwnerId, FileDTO $fileDTO): string
    {
        $paymentCode = $this->filePaymentCodeExtractor->extractFromFilePath($fileDTO->path);

        if ($paymentCode) {
            $this->documentDAO->create($fileDTO->name, $paymentCode, $documentOwnerId);

            return $paymentCode;
        }

        throw new ExtractPaymentCodeException;
    }
}
