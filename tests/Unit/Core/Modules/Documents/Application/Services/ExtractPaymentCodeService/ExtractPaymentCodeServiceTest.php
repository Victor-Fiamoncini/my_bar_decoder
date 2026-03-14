<?php

use App\Core\Modules\Documents\Application\Adapter\DocumentDAO\DocumentDAO;
use App\Core\Modules\Documents\Application\Adapter\DocumentDAO\DTOs\CreateDocumentDTO;
use App\Core\Modules\Documents\Application\Adapter\FileTextExtractor;
use App\Core\Modules\Documents\Application\Services\ExtractPaymentCodeService\Exceptions\FailedToExtractPaymentCodeException;
use App\Core\Modules\Documents\Application\Services\ExtractPaymentCodeService\ExtractPaymentCodeService;
use App\Core\Modules\Documents\Domain\Entities\PaymentCode\Exceptions\ExtractCodeException;
use App\Core\Modules\Documents\Domain\UseCases\ExtractPaymentCodeUseCase\Input\File;
use App\Core\Modules\Documents\Domain\UseCases\ExtractPaymentCodeUseCase\Input\Input;
use App\Core\Modules\Documents\Domain\UseCases\ExtractPaymentCodeUseCase\Input\PaymentCodeOwner;
use App\Core\Modules\Documents\Domain\UseCases\ExtractPaymentCodeUseCase\Output\Output;

beforeEach(function () {
    $this->fileTextExtractor = Mockery::mock(FileTextExtractor::class);
    $this->documentDAO = Mockery::mock(DocumentDAO::class);
    $this->service = new ExtractPaymentCodeService($this->fileTextExtractor, $this->documentDAO);
});

afterEach(function () {
    Mockery::close();
});

test('successfully extracts payment code and creates document', function () {
    $input = new Input(
        file: new File(name: 'test.pdf', path: '/path/to/test.pdf'),
        paymentCodeOwner: new PaymentCodeOwner(id: 1)
    );
    $expectedPaymentCode = '12345678901234567890123456789012345678901234567';

    $this->fileTextExtractor
        ->shouldReceive('extractFromFilePath')
        ->once()
        ->with($input->file->path)

        ->andReturn($expectedPaymentCode);

    $this->documentDAO
        ->shouldReceive('create')
        ->once()
        ->with(Mockery::on(function (CreateDocumentDTO $dto) use ($input, $expectedPaymentCode) {
            return $dto->name === $input->file->name
                && $dto->code === $expectedPaymentCode
                && $dto->userId === $input->paymentCodeOwner->id;
        }))
        ->andReturn(true);

    $output = $this->service->execute($input);

    expect($output)
        ->toBeInstanceOf(Output::class)
        ->and($output->paymentCode->code)
        ->toBe($expectedPaymentCode);
});

test('throws exception when payment code extraction fails', function () {
    $input = new Input(
        file: new File(name: 'test.pdf', path: '/path/to/test.pdf'),
        paymentCodeOwner: new PaymentCodeOwner(id: 1)
    );

    $this->fileTextExtractor
        ->shouldReceive('extractFromFilePath')
        ->once()
        ->with($input->file->path)
        ->andThrow(new \Exception);

    $this->documentDAO->shouldNotReceive('create');

    $this->service->execute($input);
})->throws(\Exception::class);

test('does not create document when extraction returns empty string', function () {
    $input = new Input(
        file: new File(name: 'test.pdf', path: '/path/to/test.pdf'),
        paymentCodeOwner: new PaymentCodeOwner(id: 1)
    );

    $this->fileTextExtractor
        ->shouldReceive('extractFromFilePath')
        ->once()
        ->with($input->file->path)
        ->andReturn('');

    $this->documentDAO->shouldNotReceive('create');

    $this->service->execute($input);
})->throws(FailedToExtractPaymentCodeException::class);

test('throws exception when text does not contain valid payment code', function () {
    $input = new Input(
        file: new File(name: 'test.pdf', path: '/path/to/test.pdf'),
        paymentCodeOwner: new PaymentCodeOwner(id: 1)
    );

    $this->fileTextExtractor
        ->shouldReceive('extractFromFilePath')
        ->once()
        ->with($input->file->path)
        ->andReturn('Invalid text without payment code');

    $this->documentDAO->shouldNotReceive('create');

    $this->service->execute($input);
})->throws(ExtractCodeException::class);
