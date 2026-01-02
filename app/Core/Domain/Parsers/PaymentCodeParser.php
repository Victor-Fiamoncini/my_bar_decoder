<?php

namespace App\Core\Domain\Parsers;

final class PaymentCodeParser
{
    public function parseFromText(string $text): ?string
    {
        // Brazilian DAS Barcode (48 digits)
        if ($code = $this->extractDasCode($text)) {
            return $code;
        }

        // Brazilian standard Bill Document (47 digits)
        if ($code = $this->extractBillCode($text)) {
            return $code;
        }

        return null;
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
