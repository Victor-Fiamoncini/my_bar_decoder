<?php

namespace App\Core\Infra;

use App\Core\Services\Contracts\FileBarcodeExtractor;
use Smalot\PdfParser\Parser;

class SmalotFileBarcodeExtractor implements FileBarcodeExtractor
{
    private readonly Parser $pdfParser;

    public function __construct()
    {
        $this->pdfParser = new Parser;
    }

    public function extractFromFile(string $file): string
    {
        $pdf = $this->pdfParser->parseContent($file);

        $text = $pdf->getText();
        $normalizedText = $this->normalizeText($text);

        dd($this->findLinhaDigitavel($normalizedText));

        return '';
    }

    private function normalizeText(string $text): string
    {
        $text = preg_replace('/\R+/', ' ', $text);
        $text = preg_replace('/\s+/', ' ', $text);

        return trim($text);
    }

    private function findLinhaDigitavel(string $text): array
    {
        $digits = preg_replace('/\D/', '', $text);
        $found = [];

        for ($i = 0; $i <= strlen($digits) - 47; $i++) {
            $candidate = substr($digits, $i, 47);

            // Currency digit must be 9 (BRL)
            if ($candidate[3] !== '9') {
                continue;
            }

            if ($this->validateLinhaDigitavel($candidate)) {
                $found[] = $candidate;
            }
        }

        return array_values(array_unique($found));
    }

    private function validateLinhaDigitavel(string $linha): bool
    {
        if (strlen($linha) !== 47) {
            return false;
        }

        // Fields with Modulo 10
        $campo1 = substr($linha, 0, 9);
        $dv1 = (int) $linha[9];

        $campo2 = substr($linha, 10, 10);
        $dv2 = (int) $linha[20];

        $campo3 = substr($linha, 21, 10);
        $dv3 = (int) $linha[31];

        // Barcode with Modulo 11
        $barcode = $this->linhaToBarcode($linha);
        $dvGeral = (int) $linha[32];

        return
            $this->modulo10($campo1) === $dv1 &&
            $this->modulo10($campo2) === $dv2 &&
            $this->modulo10($campo3) === $dv3 &&
            $this->modulo11($barcode) === $dvGeral;
    }

    private function linhaToBarcode(string $linha): string
    {
        return
            substr($linha, 0, 4).           // bank + currency
            substr($linha, 32, 1).          // DV geral
            substr($linha, 33, 14).         // due date + value
            substr($linha, 4, 5).           // free field 1
            substr($linha, 10, 10).         // free field 2
            substr($linha, 21, 10);          // free field 3
    }

    private function modulo10(string $number): int
    {
        $sum = 0;
        $factor = 2;

        for ($i = strlen($number) - 1; $i >= 0; $i--) {
            $add = (int) $number[$i] * $factor;
            if ($add > 9) {
                $add -= 9;
            }
            $sum += $add;
            $factor = $factor === 2 ? 1 : 2;
        }

        return (10 - ($sum % 10)) % 10;
    }

    private function modulo11(string $barcode): int
    {
        $sum = 0;
        $weight = 2;

        for ($i = strlen($barcode) - 1; $i >= 0; $i--) {
            if ($i === 4) {
                continue;
            } // Skip DV position
            $sum += (int) $barcode[$i] * $weight;
            $weight = $weight === 9 ? 2 : $weight + 1;
        }

        $dv = 11 - ($sum % 11);

        return in_array($dv, [0, 10, 11]) ? 1 : $dv;
    }
}
