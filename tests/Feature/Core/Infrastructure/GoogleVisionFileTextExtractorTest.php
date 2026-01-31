<?php

use App\Core\Domain\Entities\PaymentCode;
use App\Core\Infrastructure\GoogleVisionFileTextExtractor;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    if (empty(config('services.google_vision.api_key'))) {
        $this->markTestSkipped('Google Vision API key not configured');
    }

    Storage::fake('local');

    $this->extractor = new GoogleVisionFileTextExtractor;
});

it('extracts payment code from the document-01.pdf file successfully', function () {
    $pdfPath = base_path('tests/Fixtures/document-01.pdf');
    $extractorResult = $this->extractor->extractFromFilePath($pdfPath);
    $paymentCode = new PaymentCode($extractorResult);

    expect($extractorResult)
        ->toBeString()
        ->and($paymentCode->code)
        ->toBe('826700000043386600130002000000010058586326010368');
});

it('extracts payment code from the document-02.pdf file successfully', function () {
    $pdfPath = base_path('tests/Fixtures/document-02.pdf');
    $extractorResult = $this->extractor->extractFromFilePath($pdfPath);
    $paymentCode = new PaymentCode($extractorResult);

    expect($extractorResult)
        ->toBeString()
        ->and($paymentCode->code)
        ->toBe('23790348009012505891855013613603113610000023876');
});

it('extracts payment code from the document-03.pdf file successfully', function () {
    $pdfPath = base_path('tests/Fixtures/document-03.pdf');
    $extractorResult = $this->extractor->extractFromFilePath($pdfPath);
    $paymentCode = new PaymentCode($extractorResult);

    expect($extractorResult)
        ->toBeString()
        ->and($paymentCode->code)
        ->toBe('08591150084004849460900012030011513550000040900');
});

it('extracts payment code from the document-04.pdf file successfully', function () {
    $pdfPath = base_path('tests/Fixtures/document-04.pdf');
    $extractorResult = $this->extractor->extractFromFilePath($pdfPath);
    $paymentCode = new PaymentCode($extractorResult);

    expect($extractorResult)
        ->toBeString()
        ->and($paymentCode->code)
        ->toBe('00190000090334505900445377277178212400000014255');
});

it('extracts payment code from the document-05.pdf file successfully', function () {
    $pdfPath = base_path('tests/Fixtures/document-05.pdf');
    $extractorResult = $this->extractor->extractFromFilePath($pdfPath);
    $paymentCode = new PaymentCode($extractorResult);

    expect($extractorResult)
        ->toBeString()
        ->and($paymentCode->code)
        ->toBe('858700000111220003282608200720260097129605459740');
});

it('throws exception when PDF file does not exist', function () {
    $extractor = new GoogleVisionFileTextExtractor;
    $nonExistentPath = base_path('completely_non_existent_file.pdf');

    expect(fn () => $extractor->extractFromFilePath($nonExistentPath))->toThrow(Exception::class);
});
