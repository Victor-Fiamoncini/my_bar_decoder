<?php

namespace App\Core\Application\Services\ExtractPaymentCode;

use App\Core\Application\Adapter\DocumentDAO;
use App\Core\Application\Adapter\FileTextExtractor;
use App\Core\Application\Services\ExtractPaymentCode\DTOs\FileDTO;
use App\Core\Application\Services\ExtractPaymentCode\Exceptions\ExtractFileTextException;
use App\Core\Domain\Entities\Exceptions\ExtractPaymentCodeException;
use App\Core\Domain\Entities\PaymentCode;

class ExtractPaymentCodeService
{
    public function __construct(private readonly FileTextExtractor $fileTextExtractor, private readonly DocumentDAO $documentDAO) {}

    /**
     * @throws ExtractFileTextException
     * @throws ExtractPaymentCodeException
     */
    public function execute(int $documentOwnerId, FileDTO $fileDTO): PaymentCode
    {
        $fileText = $this->fileTextExtractor->extractFromFilePath($fileDTO->path);

        if ($fileText) {
            $paymentCode = new PaymentCode($fileText);

            $this->documentDAO->create($fileDTO->name, $paymentCode->code, $documentOwnerId);

            return $paymentCode;
        }

        throw new ExtractFileTextException;
    }
}
