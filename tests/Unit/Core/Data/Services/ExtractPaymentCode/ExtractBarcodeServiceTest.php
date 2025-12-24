<?php

use App\Core\Data\Adapter\DocumentDAO;
use App\Core\Data\Adapter\FilePaymentCodeExtractor;
use App\Core\Data\Services\ExtractPaymentCode\DTOs\FileDTO;
use App\Core\Data\Services\ExtractPaymentCode\Exceptions\ExtractPaymentCodeException;
use App\Core\Data\Services\ExtractPaymentCode\ExtractPaymentCodeService;

beforeEach(function () {
    $this->filePaymentCodeExtractor = Mockery::mock(FilePaymentCodeExtractor::class);
    $this->documentDAO = Mockery::mock(DocumentDAO::class);
    $this->service = new ExtractPaymentCodeService($this->filePaymentCodeExtractor, $this->documentDAO);
});

afterEach(function () {
    Mockery::close();
});

test('successfully extracts payment code and creates document', function () {
    $documentOwnerId = 1;
    $fileDTO = new FileDTO(name: 'test.pdf', path: '/path/to/test.pdf');
    $expectedPaymentCode = '12345678901234567890123456789012';

    $this->filePaymentCodeExtractor
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

    expect($result)->toBe($expectedPaymentCode);
});

test('throws exception when payment code extraction fails', function () {
    $documentOwnerId = 1;
    $fileDTO = new FileDTO(name: 'test.pdf', path: '/path/to/test.pdf');

    $this->filePaymentCodeExtractor
        ->shouldReceive('extractFromFilePath')
        ->once()
        ->with($fileDTO->path)
        ->andThrow(new \Exception);

    $this->documentDAO
        ->shouldNotReceive('create');

    $this->service->execute($documentOwnerId, $fileDTO);
})->throws(ExtractPaymentCodeException::class);

test('does not create document when extraction returns empty string', function () {
    $documentOwnerId = 1;
    $fileDTO = new FileDTO(name: 'test.pdf', path: '/path/to/test.pdf');

    $this->filePaymentCodeExtractor
        ->shouldReceive('extractFromFilePath')
        ->once()
        ->with($fileDTO->path)
        ->andReturn('');

    $this->documentDAO
        ->shouldNotReceive('create');

    $this->service->execute($documentOwnerId, $fileDTO);
})->throws(ExtractPaymentCodeException::class);
