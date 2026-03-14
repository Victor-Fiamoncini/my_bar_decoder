<?php

namespace App\Core\Modules\Documents\Infrastructure;

use App\Core\Modules\Documents\Application\Adapter\FileTextExtractor;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleVisionFileTextExtractor implements FileTextExtractor
{
    private string $googleVisionApiUrl;

    public function __construct()
    {
        $apiKey = config('services.google_vision.api_key');

        $this->googleVisionApiUrl = "https://vision.googleapis.com/v1/images:annotate?key={$apiKey}";
    }

    /**
     * @throws \Exception
     */
    public function extractFromFilePath(string $filePath): string
    {
        $tempImagePath = $this->generateTempFilePath();

        try {
            $this->tryConvertPdfToImage($filePath, $tempImagePath);

            return $this->extractContentFromBase64File($this->encodeFileToBase64($tempImagePath));
        } catch (\Throwable $t) {
            Log::error($t->getMessage());

            throw new \Exception('Failed to extract text content from file');
        } finally {
            if (file_exists($tempImagePath)) {
                unlink($tempImagePath);
            }
        }
    }

    private function generateTempFilePath(): string
    {
        return storage_path('app/temp_'.uniqid().'.png');
    }

    /**
     * @throws \Exception
     */
    private function tryConvertPdfToImage(string $pdfPath, string $imagePath): void
    {
        $command = sprintf(
            'gs -dSAFER -dBATCH -dNOPAUSE -sDEVICE=png16m -r300 -dFirstPage=1 -dLastPage=1 -sOutputFile=%s %s 2>&1',
            escapeshellarg($imagePath),
            escapeshellarg($pdfPath)
        );

        exec($command, $output, $resultCode);

        if ($resultCode !== 0) {
            throw new \Exception('Failed to convert PDF to PNG');
        }
    }

    private function encodeFileToBase64(string $filePath): string
    {
        return base64_encode(file_get_contents($filePath));
    }

    /**
     * @throws ConnectionException
     * @throws \Exception
     */
    private function extractContentFromBase64File(string $base64Content): string
    {
        $response = Http::timeout(60)->post($this->googleVisionApiUrl, [
            'requests' => [
                [
                    'image' => ['content' => $base64Content],
                    'features' => [['type' => 'TEXT_DETECTION', 'maxResults' => 1]],
                ],
            ],
        ]);

        if ($response->failed()) {
            throw new \Exception('Failed to extract text content from base64 PNG');
        }

        $fileText = $response->json('responses.0.textAnnotations.0.description', '');

        if ($fileText) {
            return $fileText;
        }

        throw new \Exception('Extracted file text not found');
    }
}
