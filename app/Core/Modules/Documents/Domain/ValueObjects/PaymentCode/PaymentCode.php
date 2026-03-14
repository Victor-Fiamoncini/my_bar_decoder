<?php

namespace App\Core\Modules\Documents\Domain\ValueObjects\PaymentCode;

use App\Core\Modules\Documents\Domain\ValueObjects\PaymentCode\Exceptions\ExtractCodeException;

readonly class PaymentCode
{
    private function __construct(public string $code) {}

    /**
     * @throws ExtractCodeException
     */
    public static function tryCreateFromText(string $text): self
    {
        if ($dasCode = self::extractDasCode($text)) {
            return new self($dasCode);
        }

        if ($billCode = self::extractBillCode($text)) {
            return new self($billCode);
        }

        throw new ExtractCodeException;
    }

    private static function extractDasCode(string $text): ?string
    {
        $dasPattern = '/(\d{11}\s*\d\s*\d{11}\s*\d\s*\d{11}\s*\d\s*\d{11}\s*\d)/';

        preg_match($dasPattern, $text, $matches);

        if (empty($matches)) {
            return null;
        }

        return preg_replace('/\D/', '', $matches[0]);
    }

    private static function extractBillCode(string $text): ?string
    {
        $standardBillPattern = '/(\d{5}[.\s]?\d{5}[.\s]?\d{5}[.\s]?\d{6}[.\s]?\d{5}[.\s]?\d{6}[.\s]?\d[.\s]?\d{14})/';

        preg_match($standardBillPattern, $text, $matches);

        if (empty($matches)) {
            return null;
        }

        return preg_replace('/\D/', '', $matches[0]);
    }
}
