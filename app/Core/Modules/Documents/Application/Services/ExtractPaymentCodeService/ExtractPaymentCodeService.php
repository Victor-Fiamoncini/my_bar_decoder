<?php

namespace App\Core\Modules\Documents\Application\Services\ExtractPaymentCodeService;

use App\Core\Modules\Documents\Application\Adapter\DocumentDAO\DocumentDAO;
use App\Core\Modules\Documents\Application\Adapter\DocumentDAO\DTOs\CreateDocumentDTO;
use App\Core\Modules\Documents\Application\Adapter\FileTextExtractor;
use App\Core\Modules\Documents\Application\Services\ExtractPaymentCodeService\Exceptions\FailedToExtractPaymentCodeException;
use App\Core\Modules\Documents\Domain\Entities\PaymentCode\Exceptions\ExtractCodeException;
use App\Core\Modules\Documents\Domain\Entities\PaymentCode\PaymentCode;
use App\Core\Modules\Documents\Domain\UseCases\ExtractPaymentCodeUseCase\ExtractPaymentCodeUseCase;
use App\Core\Modules\Documents\Domain\UseCases\ExtractPaymentCodeUseCase\Input\Input;
use App\Core\Modules\Documents\Domain\UseCases\ExtractPaymentCodeUseCase\Output\Output;

readonly class ExtractPaymentCodeService implements ExtractPaymentCodeUseCase
{
    public function __construct(private FileTextExtractor $fileTextExtractor, private DocumentDAO $documentDAO) {}

    /**
     * @throws ExtractCodeException
     * @throws FailedToExtractPaymentCodeException
     */
    public function execute(Input $input): Output
    {
        $fileText = $this->fileTextExtractor->extractFromFilePath($input->file->path);

        if ($fileText) {
            $paymentCode = PaymentCode::tryCreateFromText($fileText);

            $createDocumentDTO = new CreateDocumentDTO(
                name: $input->file->name,
                code: $paymentCode->code,
                userId: $input->paymentCodeOwner->id
            );

            $this->documentDAO->create($createDocumentDTO);

            return new Output($paymentCode);
        }

        throw new FailedToExtractPaymentCodeException;
    }
}
