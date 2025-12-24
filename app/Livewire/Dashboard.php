<?php

namespace App\Livewire;

use App\Core\Services\DTOs\FileDTO;
use App\Core\Services\Exceptions\ExtractPaymentCodeException;
use App\Core\Services\ExtractBarcodeService;
use App\Http\Requests\UploadDocumentRequest;
use Livewire\Component;
use Livewire\WithFileUploads;

class Dashboard extends Component
{
    use WithFileUploads;

    public $file;

    public string $paymentCode = '';

    public function submit(ExtractBarcodeService $extractBarcodeService): void
    {
        $this->paymentCode = '';

        $validated = $this->validate(
            (new UploadDocumentRequest)->rules(),
            (new UploadDocumentRequest)->messages()
        );

        try {
            $this->paymentCode = $extractBarcodeService->execute(
                new FileDTO(
                    name: $validated['file']->getClientOriginalName(),
                    path: $validated['file']->getRealPath()
                )
            );
        } catch (ExtractPaymentCodeException $e) {
            $this->addError('file', $e->getMessage());
        }
    }
}
