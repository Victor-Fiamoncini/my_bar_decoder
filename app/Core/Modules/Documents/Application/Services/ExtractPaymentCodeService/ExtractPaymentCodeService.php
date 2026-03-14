<?php

namespace App\Core\Modules\Documents\Application\Services\ExtractPaymentCodeService;

use App\Core\Modules\Documents\Application\Adapter\FileTextExtractor;
use App\Core\Modules\Documents\Application\Services\ExtractPaymentCodeService\Exceptions\FailedToExtractPaymentCodeException;
use App\Core\Modules\Documents\Domain\Entities\Document\Document;
use App\Core\Modules\Documents\Domain\Repositories\DocumentRepository;
use App\Core\Modules\Documents\Domain\UseCases\ExtractPaymentCodeUseCase\ExtractPaymentCodeUseCase;
use App\Core\Modules\Documents\Domain\UseCases\ExtractPaymentCodeUseCase\Input\Input;
use App\Core\Modules\Documents\Domain\UseCases\ExtractPaymentCodeUseCase\Output\Output;
use App\Core\Modules\Documents\Domain\ValueObjects\PaymentCode\Exceptions\ExtractCodeException;
use App\Core\Modules\Documents\Domain\ValueObjects\PaymentCode\PaymentCode;

readonly class ExtractPaymentCodeService implements ExtractPaymentCodeUseCase
{
    public function __construct(private FileTextExtractor $fileTextExtractor, private DocumentRepository $documentRepository) {}

    /**
     * @throws ExtractCodeException
     * @throws FailedToExtractPaymentCodeException
     */
    public function execute(Input $input): Output
    {
        $fileText = $this->fileTextExtractor->extractFromFilePath($input->file->path);

        if ($fileText) {
            $paymentCode = PaymentCode::tryCreateFromText($fileText);

            $document = new Document(
                name: $input->file->name,
                paymentCode: $paymentCode,
                createdAt: new \DateTimeImmutable,
                ownerId: $input->paymentCodeOwner->id,
            );

            $this->documentRepository->save($document);

            return new Output($document);
        }

        throw new FailedToExtractPaymentCodeException;
    }
}
