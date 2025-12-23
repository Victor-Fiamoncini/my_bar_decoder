<?php

namespace App\Livewire;

use App\Core\Services\DTOs\FileDTO;
use App\Core\Services\Exceptions\ExtractPaymentCodeException;
use App\Core\Services\ExtractBarcodeService;
use Livewire\Component;
use Livewire\WithFileUploads;

class Dashboard extends Component
{
    use WithFileUploads;

    public $file;

    public string $paymentCode = '';

    /**
     * @throws ExtractPaymentCodeException
     */
    public function submit(ExtractBarcodeService $extractBarcodeService): void
    {
        $this->paymentCode = '';

        $validated = $this->validate(['file' => ['required', 'file', 'mimes:pdf', 'max:5120']]);

        $this->paymentCode = $extractBarcodeService->execute(
            new FileDTO(
                name: $validated['file']->getClientOriginalName(),
                path: $validated['file']->getRealPath()
            )
        );
    }
}
