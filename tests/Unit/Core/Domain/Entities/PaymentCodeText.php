<?php

use App\Core\Domain\Entities\Exceptions\ExtractPaymentCodeException;
use App\Core\Domain\Entities\PaymentCode;

describe('PaymentCode', function () {
    it('extracts DAS barcode with 48 digits without spaces', function () {
        $text = '123456789011234567890112345678901123456789012345';
        $paymentCode = new PaymentCode($text);

        expect($paymentCode->code)->toBe('123456789011234567890112345678901123456789012345');
    });

    it('extracts DAS barcode with spaces', function () {
        $text = '12345678901 1 12345678901 1 12345678901 1 12345678901 2';
        $paymentCode = new PaymentCode($text);

        expect($paymentCode->code)->toBe('123456789011123456789011123456789011123456789012');
    });

    it('extracts standard bill code with 47 digits without separators', function () {
        $text = '12345678901234567890123456789012345678901234567';
        $paymentCode = new PaymentCode($text);

        expect($paymentCode->code)->toBe('12345678901234567890123456789012345678901234567');
    });

    it('extracts standard bill code with dots and spaces', function () {
        $text = '12345.67890 12345.678901 23456.789012 3 45678901234567';
        $paymentCode = new PaymentCode($text);

        expect($paymentCode->code)->toBe('12345678901234567890123456789012345678901234567');
    });

    it('extracts code from text with additional content', function () {
        $text = 'Payment code: 12345.67890 12345.678901 23456.789012 3 45678901234567 - Pay before due date';
        $paymentCode = new PaymentCode($text);

        expect($paymentCode->code)->toBe('12345678901234567890123456789012345678901234567');
    });

    it('throws exception when no valid code is found', function () {
        $text = 'This is just random text without any valid payment code';

        expect(fn () => new PaymentCode($text))
            ->toThrow(ExtractPaymentCodeException::class);
    });

    it('throws exception for incomplete DAS code', function () {
        $text = '12345678901 1 12345678901 1 12345';

        expect(fn () => new PaymentCode($text))
            ->toThrow(ExtractPaymentCodeException::class);
    });

    it('throws exception for incomplete bill code', function () {
        $text = '12345.67890 12345.678901';

        expect(fn () => new PaymentCode($text))
            ->toThrow(ExtractPaymentCodeException::class);
    });
});
