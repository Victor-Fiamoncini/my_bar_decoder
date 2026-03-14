<?php

namespace App\Core\Modules\Documents\Domain\Entities\PaymentCode;

use App\Core\Modules\Documents\Domain\Entities\PaymentCode\Exceptions\ExtractCodeException;

readonly class PaymentCode
{
    public string $code;

    private function __construct() {}

    /**
     * @throws ExtractCodeException
     */
    public static function tryCreateFromText(string $text): self
    {
        $paymentCode = new PaymentCode;
        $paymentCode->tryToExtractCodeFromText($text);

        return $paymentCode;
    }

    /**
     * @throws ExtractCodeException
     */
    private function tryToExtractCodeFromText(string $text): void
    {
        // Brazilian DAS Barcode (48 digits)
        if ($dasCode = $this->extractDasCode($text)) {
            $this->code = $dasCode;

            return;
        }

        // Brazilian standard Bill Document (47 digits)
        if ($brazilianBillCode = $this->extractBillCode($text)) {
            $this->code = $brazilianBillCode;

            return;
        }

        throw new ExtractCodeException;
    }

    private function extractDasCode(string $text): ?string
    {
        $dasPattern = '/(\d{11}\s*\d{1}\s*\d{11}\s*\d{1}\s*\d{11}\s*\d{1}\s*\d{11}\s*\d{1})/';

        preg_match($dasPattern, $text, $matches);

        if (empty($matches)) {
            return null;
        }

        return preg_replace('/[^\d]/', '', $matches[0]);
    }

    private function extractBillCode(string $text): ?string
    {
        $standardBillPattern = '/(\d{5}[\.\s]?\d{5}[\.\s]?\d{5}[\.\s]?\d{6}[\.\s]?\d{5}[\.\s]?\d{6}[\.\s]?\d[\.\s]?\d{14})/';

        preg_match($standardBillPattern, $text, $matches);

        if (empty($matches)) {
            return null;
        }

        return preg_replace('/[^\d]/', '', $matches[0]);
    }
}
