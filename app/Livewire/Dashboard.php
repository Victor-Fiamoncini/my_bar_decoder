<?php

namespace App\Livewire;

use App\Core\Application\Services\ExtractPaymentCode\DTOs\FileDTO;
use App\Core\Application\Services\ExtractPaymentCode\ExtractPaymentCodeService;
use App\Core\Domain\Entities\Exceptions\ExtractPaymentCodeException;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Dashboard extends Component
{
    use WithFileUploads, WithPagination;

    public $files = [];

    public array $paymentCodes = [];

    public array $errors = [];

    protected function rules(): array
    {
        return [
            'files' => 'present|array|min:1|max:10',
            'files.*' => 'file|mimes:pdf|max:5120',
        ];
    }

    protected function messages(): array
    {
        return [
            'files.present' => 'At least one file is required',
            'files.array' => 'Files must be provided as an array',
            'files.min' => 'At least one file is required',
            'files.max' => 'Maximum 10 files allowed',
            'files.*.file' => 'Each upload must be a valid file',
            'files.*.mimes' => 'Each file must be a PDF',
            'files.*.max' => 'Each file must not exceed 5MB',
        ];
    }

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

        $this->paymentCodes = [];
        $this->errors = [];

        foreach ($validated['files'] as $file) {
            try {
                $extractedPaymentCode = $extractBarcodeService->execute(
                    auth()->id(),
                    new FileDTO(
                        name: $file->getClientOriginalName(),
                        path: $file->getRealPath()
                    )
                );

                $this->paymentCodes[] = [
                    'name' => $file->getClientOriginalName(),
                    'code' => $extractedPaymentCode->code,
                ];
            } catch (ExtractPaymentCodeException $e) {
                $this->errors[] = [
                    'name' => $file->getClientOriginalName(),
                    'message' => __('Failed to extract payment code from :file', ['file' => $file->getClientOriginalName()]),
                ];
            } catch (\Throwable $t) {
                $this->errors[] = [
                    'name' => $file->getClientOriginalName(),
                    'message' => __('Failed to process :file', ['file' => $file->getClientOriginalName()]),
                ];
            }
        }

        $this->reset('files');
        $this->resetPage();
    }
}
