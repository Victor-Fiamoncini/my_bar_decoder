<?php

namespace App\Core\Infra;

use App\Core\Data\Adapter\FilePaymentCodeExtractor;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

readonly class GoogleVisionFilePaymentCodeExtractor implements FilePaymentCodeExtractor
{
    private string $googleVisionApiUrl;

    public function __construct()
    {
        $apiKey = config('services.google_vision.api_key');

        $this->googleVisionApiUrl = "https://vision.googleapis.com/v1/images:annotate?key={$apiKey}";
    }

    /**
     * @throws Exception|Throwable
     */
    public function extractFromFilePath(string $filePath): string
    {
        $tempImagePath = storage_path('app/temp_'.uniqid().'.png');

        try {
            $this->tryConvertPdfToImage($filePath, $tempImagePath);

            $imageBase64Content = base64_encode(file_get_contents($tempImagePath));

            $response = Http::timeout(60)->post($this->googleVisionApiUrl, [
                'requests' => [
                    [
                        'image' => ['content' => $imageBase64Content],
                        'features' => [['type' => 'TEXT_DETECTION', 'maxResults' => 1]],
                    ],
                ],
            ]);

            if ($response->failed()) {
                throw new Exception('Failed to extract text content from PNG');
            }

            $text = $response->json('responses.0.textAnnotations.0.description', '');

            return $this->tryExtractPaymentCode($text);
        } catch (Exception $e) {
            Log::error($e->getMessage());

            throw new Exception('Failed to extract payment code');
        } finally {
            unlink($tempImagePath);
        }
    }

    /**
     * @throws Exception
     * @throws Throwable
     */
    private function tryConvertPdfToImage(string $pdfPath, string $imagePath): void
    {
        try {
            $command = sprintf(
                'gs -dSAFER -dBATCH -dNOPAUSE -sDEVICE=png16m -r300 -dFirstPage=1 -dLastPage=1 -sOutputFile=%s %s 2>&1',
                escapeshellarg($imagePath),
                escapeshellarg($pdfPath)
            );

            exec($command, $output, $returnVar);

            Log::info($returnVar, $output);

            if ($returnVar !== 0 || ! file_exists($imagePath)) {
                throw new Exception('Failed to convert PDF to PNG');
            }
        } catch (Throwable $t) {
            Log::error($t->getMessage());

            throw $t;
        }
    }

    /**
     * @throws Exception
     */
    private function tryExtractPaymentCode(string $text): string
    {
        // Try to extract DAS barcode (48 digits)
        $dasPattern = '/(\d{11}\s*\d{1}\s*\d{11}\s*\d{1}\s*\d{11}\s*\d{1}\s*\d{11}\s*\d{1})/';

        preg_match($dasPattern, $text, $matches);

        if (! empty($matches)) {
            return preg_replace('/[^\d]/', '', $matches[0]);
        }

        // Fallback to boleto pattern (47 digits)
        $boletoPattern = '/(\d{5}[\.\s]?\d{5}[\.\s]?\d{5}[\.\s]?\d{6}[\.\s]?\d{5}[\.\s]?\d{6}[\.\s]?\d[\.\s]?\d{14})/';

        preg_match($boletoPattern, $text, $matches);

        if (! empty($matches)) {
            return preg_replace('/[^\d]/', '', $matches[0]);
        }

        throw new Exception('Payment code not found in the document');
    }
}
