<?php

namespace App\Livewire;

use App\Core\Data\Services\ExtractPaymentCode\DTOs\FileDTO;
use App\Core\Data\Services\ExtractPaymentCode\Exceptions\ExtractPaymentCodeException;
use App\Core\Data\Services\ExtractPaymentCode\ExtractPaymentCodeService;
use App\Models\Document;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class Dashboard extends Component
{
    use WithFileUploads;

    #[Validate('required', message: 'An attached file is required')]
    #[Validate('file', message: 'The attached file must be a valid file')]
    #[Validate('mimes:pdf', message: 'The attached file must be a PDF')]
    #[Validate('max:5120', message: 'The maximum attached file size is 5MB')]
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

    public function submit(ExtractPaymentCodeService $extractBarcodeService): void
    {
        $validated = $this->validate();

        try {
            $this->paymentCode = $extractBarcodeService->execute(
                auth()->id(),
                new FileDTO(
                    name: $validated['file']->getClientOriginalName(),
                    path: $validated['file']->getRealPath()
                )
            );

            $this->loadDocuments();
        } catch (ExtractPaymentCodeException $e) {
            $this->addError('file', $e->getMessage());
        } catch (\Exception $e) {
            $this->addError('file', 'Failed to process the attached file. Please try again.');
        } finally {
            $this->reset('file');
        }
    }
}
