<?php

use App\Core\Domain\Parsers\PaymentCodeParser;

describe('PaymentCodeParser', function () {
    beforeEach(function () {
        $this->parser = new PaymentCodeParser;
    });

    describe('parseFromText', function () {
        it('extracts DAS code with 48 digits', function () {
            $text = '12345678901 2 34567890123 4 56789012345 6 78901234567 8';
            $result = $this->parser->parseFromText($text);

            expect($result)->toBe('123456789012345678901234567890123456789012345678');
        });

        it('extracts DAS code without spaces', function () {
            $text = '123456789012345678901234567890123456789012345678';
            $result = $this->parser->parseFromText($text);

            expect($result)->toBe('123456789012345678901234567890123456789012345678');
        });

        it('extracts bill code with 47 digits and dots', function () {
            $text = '12345.67890 12345.678901 23456.789012 3 12345678901234';
            $result = $this->parser->parseFromText($text);

            expect($result)->toBe('12345678901234567890123456789012312345678901234');
        });

        it('extracts bill code with spaces', function () {
            $text = '12345 67890 12345 678901 23456 789012 3 12345678901234';
            $result = $this->parser->parseFromText($text);

            expect($result)->toBe('12345678901234567890123456789012312345678901234');
        });

        it('extracts bill code without separators', function () {
            $text = '12345678901234567890123456789012312345678901234';
            $result = $this->parser->parseFromText($text);

            expect($result)->toBe('12345678901234567890123456789012312345678901234');
        });

        it('prioritizes DAS code over bill code when both are present', function () {
            $dasCode = '123456789012345678901234567890123456789012345678';
            $billCode = '12345678901234567890123456789012312345678901234';
            $text = "DAS: {$dasCode}\nBill: {$billCode}";

            $result = $this->parser->parseFromText($text);

            expect($result)->toBe($dasCode);
        });

        it('returns null when no payment code is found', function () {
            $text = 'Random text without payment codes';
            $result = $this->parser->parseFromText($text);

            expect($result)->toBeNull();
        });

        it('returns null for incomplete DAS code', function () {
            $text = '1234567890123456789012345678901234567890123456'; // 46 digits
            $result = $this->parser->parseFromText($text);

            expect($result)->toBeNull();
        });

        it('returns null for incomplete bill code', function () {
            $text = '123456789012345678901234567890123123456789012'; // 45 digits
            $result = $this->parser->parseFromText($text);

            expect($result)->toBeNull();
        });

        it('handles text with extra characters around payment code', function () {
            $text = 'Payment code: 12345.67890 12345.678901 23456.789012 3 12345678901234 - Please pay';
            $result = $this->parser->parseFromText($text);

            expect($result)->toBe('12345678901234567890123456789012312345678901234');
        });
    });
});
