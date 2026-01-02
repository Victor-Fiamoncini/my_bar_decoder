<?php

namespace App\Core\Infra;

use App\Core\Data\Adapter\FilePaymentCodeExtractor;
use App\Core\Domain\Parsers\PaymentCodeParser;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

readonly class GoogleVisionFilePaymentCodeExtractor implements FilePaymentCodeExtractor
{
    private string $googleVisionApiUrl;

    private PaymentCodeParser $paymentCodeParser;

    public function __construct(PaymentCodeParser $paymentCodeParser)
    {
        $apiKey = config('services.google_vision.api_key');

        $this->googleVisionApiUrl = "https://vision.googleapis.com/v1/images:annotate?key={$apiKey}";
        $this->paymentCodeParser = $paymentCodeParser;
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

            $code = $this->paymentCodeParser->parseFromText($text);

            if ($code) {
                return $code;
            }

            throw new Exception('Payment code not found in extracted text');
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
        $command = sprintf(
            'gs -dSAFER -dBATCH -dNOPAUSE -sDEVICE=png16m -r300 -dFirstPage=1 -dLastPage=1 -sOutputFile=%s %s 2>&1',
            escapeshellarg($imagePath),
            escapeshellarg($pdfPath)
        );

        exec($command, $output, $returnVar);

        if ($returnVar !== 0 || ! file_exists($imagePath)) {
            throw new Exception('Failed to convert PDF to PNG');
        }
    }
}
