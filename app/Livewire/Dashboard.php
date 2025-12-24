<?php

namespace App\Livewire;

use App\Core\Services\DTOs\FileDTO;
use App\Core\Services\Exceptions\ExtractPaymentCodeException;
use App\Core\Services\ExtractBarcodeService;
use App\Models\Document;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class Dashboard extends Component
{
    use WithFileUploads;

    #[Validate('required', message: 'The file is required')]
    #[Validate('file', message: 'The file must be a valid file')]
    #[Validate('mimes:pdf', message: 'The file must be a PDF')]
    #[Validate('max:5120', message: 'The maximum file size is 5MB')]
    public $file;

    public string $paymentCode = '';

    /** @var Collection<int, Document> */
    public Collection $documents;

    public function mount(): void
    {
        $this->loadDocuments();
    }

    public function loadDocuments(): void
    {
        $this->documents = Document::where('user_id', auth()->id())
            ->latest()
            ->get(['id', 'name', 'code', 'created_at']);
    }

    public function submit(ExtractBarcodeService $extractBarcodeService): void
    {
        $validated = $this->validate();

        try {
            $this->paymentCode = $extractBarcodeService->execute(
                new FileDTO(
                    name: $validated['file']->getClientOriginalName(),
                    path: $validated['file']->getRealPath()
                )
            );
        } catch (ExtractPaymentCodeException $e) {
            $this->addError('file', $e->getMessage());
        } finally {
            $this->loadDocuments();
            $this->reset('file');
        }
    }
}
