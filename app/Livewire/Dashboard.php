<?php

namespace App\Livewire;

use App\Core\Services\ExtractBarcodeService;
use App\Core\Services\FileDto;
use Livewire\Component;
use Livewire\WithFileUploads;

class Dashboard extends Component
{
    use WithFileUploads;

    public $file;

    public string $barcode = '';

    public function submit(ExtractBarcodeService $extractBarcodeService): void
    {
        $validated = $this->validate(['file' => ['required', 'file', 'mimes:pdf', 'max:5120']]);

        $barcode = $extractBarcodeService->execute(
            new FileDto(
                name: $validated['file']->getClientOriginalName(),
                content: $validated['file']->get()
            )
        );

        $this->barcode = $barcode ?? '';
    }
}
