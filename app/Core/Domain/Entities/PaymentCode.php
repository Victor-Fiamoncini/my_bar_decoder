<?php

namespace App\Core\Domain\Entities;

use App\Core\Domain\Entities\Exceptions\ExtractPaymentCodeException;

class PaymentCode
{
    public readonly string $code;

    /**
     * @throws ExtractPaymentCodeException
     */
    public function __construct(string $text)
    {
        $this->tryToExtractCodeFromText($text);
    }

    /**
     * @throws ExtractPaymentCodeException
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

        throw new ExtractPaymentCodeException;
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
