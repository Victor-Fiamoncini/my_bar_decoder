<?php

namespace App\Livewire;

use App\Core\Data\Services\ExtractPaymentCode\DTOs\FileDTO;
use App\Core\Data\Services\ExtractPaymentCode\Exceptions\ExtractPaymentCodeException;
use App\Core\Data\Services\ExtractPaymentCode\ExtractPaymentCodeService;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Dashboard extends Component
{
    use WithFileUploads, WithPagination;

    #[Validate('required', message: 'An attached file is required')]
    #[Validate('file', message: 'The attached file must be a valid file')]
    #[Validate('mimes:pdf', message: 'The attached file must be a PDF')]
    #[Validate('max:5120', message: 'The maximum attached file size is 5MB')]
    public $file;

    public string $paymentCode = '';

    public function getDocumentsProperty(): LengthAwarePaginator
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->documents()
            ->latest()
            ->paginate(6, ['id', 'name', 'code', 'created_at']);
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

            $this->resetPage();

            session()->flash('success', __('Payment code extracted successfully!'));
        } catch (ExtractPaymentCodeException $e) {
            $this->addError('file', $e->getMessage());

            session()->flash('error', __($e->getMessage()));
        } catch (\Exception $e) {
            $message = 'Failed to process the attached file. Please try again.';

            $this->addError('file', $message);

            session()->flash('error', __($message));
        } finally {
            $this->reset('file');
        }
    }
}
