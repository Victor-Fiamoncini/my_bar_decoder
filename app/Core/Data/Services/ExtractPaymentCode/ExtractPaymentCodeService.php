<?php

namespace App\Core\Data\Services\ExtractPaymentCode;

use App\Core\Data\Adapter\DocumentDAO;
use App\Core\Data\Adapter\FilePaymentCodeExtractor;
use App\Core\Data\Services\ExtractPaymentCode\DTOs\FileDTO;
use App\Core\Data\Services\ExtractPaymentCode\Exceptions\ExtractPaymentCodeException;

readonly class ExtractPaymentCodeService
{
    public function __construct(private FilePaymentCodeExtractor $filePaymentCodeExtractor, private DocumentDAO $documentDAO) {}

    /**
     * @throws ExtractPaymentCodeException
     */
    public function execute(int $documentOwnerId, FileDTO $fileDTO): string
    {
        try {
            $paymentCode = $this->filePaymentCodeExtractor->extractFromFilePath($fileDTO->path);

            if ($paymentCode) {
                $this->documentDAO->create($fileDTO->name, $paymentCode, $documentOwnerId);

                return $paymentCode;
            }

            throw new ExtractPaymentCodeException;
        } catch (\Exception $e) {
            throw new ExtractPaymentCodeException;
        }
    }
}
