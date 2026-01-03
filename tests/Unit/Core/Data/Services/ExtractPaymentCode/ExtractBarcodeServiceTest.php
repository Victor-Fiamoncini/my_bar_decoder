<?php

use App\Core\Data\Adapter\DocumentDAO;
use App\Core\Data\Adapter\FileTextExtractor;
use App\Core\Data\Services\ExtractPaymentCode\DTOs\FileDTO;
use App\Core\Data\Services\ExtractPaymentCode\Exceptions\ExtractFileTextException;
use App\Core\Data\Services\ExtractPaymentCode\ExtractPaymentCodeService;
use App\Core\Domain\Entities\Exceptions\ExtractPaymentCodeException;
use App\Core\Domain\Entities\PaymentCode;

beforeEach(function () {
    $this->fileTextExtractor = Mockery::mock(FileTextExtractor::class);
    $this->documentDAO = Mockery::mock(DocumentDAO::class);
    $this->service = new ExtractPaymentCodeService($this->fileTextExtractor, $this->documentDAO);
});

afterEach(function () {
    Mockery::close();
});

test('successfully extracts payment code and creates document', function () {
    $documentOwnerId = 1;
    $fileDTO = new FileDTO(name: 'test.pdf', path: '/path/to/test.pdf');
    $expectedPaymentCode = '12345678901234567890123456789012345678901234567';

    $this->fileTextExtractor
        ->shouldReceive('extractFromFilePath')
        ->once()
        ->with($fileDTO->path)
        ->andReturn($expectedPaymentCode);

    $this->documentDAO
        ->shouldReceive('create')
        ->once()
        ->with($fileDTO->name, $expectedPaymentCode, $documentOwnerId)
        ->andReturn(true);

    $result = $this->service->execute($documentOwnerId, $fileDTO);

    expect($result)->toBeInstanceOf(PaymentCode::class)
        ->and($result->code)->toBe($expectedPaymentCode);
});

test('throws exception when payment code extraction fails', function () {
    $documentOwnerId = 1;
    $fileDTO = new FileDTO(name: 'test.pdf', path: '/path/to/test.pdf');

    $this->fileTextExtractor
        ->shouldReceive('extractFromFilePath')
        ->once()
        ->with($fileDTO->path)
        ->andThrow(new \Exception);

    $this->documentDAO
        ->shouldNotReceive('create');

    $this->service->execute($documentOwnerId, $fileDTO);
})->throws(\Exception::class);

test('does not create document when extraction returns empty string', function () {
    $documentOwnerId = 1;
    $fileDTO = new FileDTO(name: 'test.pdf', path: '/path/to/test.pdf');

    $this->fileTextExtractor
        ->shouldReceive('extractFromFilePath')
        ->once()
        ->with($fileDTO->path)
        ->andReturn('');

    $this->documentDAO
        ->shouldNotReceive('create');

    $this->service->execute($documentOwnerId, $fileDTO);
})->throws(ExtractFileTextException::class);

test('throws exception when text does not contain valid payment code', function () {
    $documentOwnerId = 1;
    $fileDTO = new FileDTO(name: 'test.pdf', path: '/path/to/test.pdf');

    $this->fileTextExtractor
        ->shouldReceive('extractFromFilePath')
        ->once()
        ->with($fileDTO->path)
        ->andReturn('Invalid text without payment code');

    $this->documentDAO
        ->shouldNotReceive('create');

    $this->service->execute($documentOwnerId, $fileDTO);
})->throws(ExtractPaymentCodeException::class);
