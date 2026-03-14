<?php

namespace App\Livewire;

use App\Core\Modules\Documents\Application\Services\ExtractPaymentCodeService\Exceptions\FailedToExtractPaymentCodeException;
use App\Core\Modules\Documents\Domain\UseCases\ExtractPaymentCodeUseCase\ExtractPaymentCodeUseCase;
use App\Core\Modules\Documents\Domain\UseCases\ExtractPaymentCodeUseCase\Input\File;
use App\Core\Modules\Documents\Domain\UseCases\ExtractPaymentCodeUseCase\Input\Input;
use App\Core\Modules\Documents\Domain\UseCases\ExtractPaymentCodeUseCase\Input\PaymentCodeOwner;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Dashboard extends Component
{
    use WithFileUploads, WithPagination;

    public $files = [];

    public array $errors = [];

    public array $extractedPaymentCodes = [];

    protected function rules(): array
    {
        return [
            'files' => 'present|array|min:1|max:5',
            'files.*' => 'file|mimes:pdf|max:5120',
        ];
    }

    protected function messages(): array
    {
        return [
            'files.present' => 'At least one file is required',
            'files.array' => 'Files must be provided as an array',
            'files.min' => 'At least one file is required',
            'files.max' => 'Maximum 5 files allowed',
            'files.*.file' => 'Each upload must be a valid file',
            'files.*.mimes' => 'Each file must be a PDF',
            'files.*.max' => 'Each file must not exceed 5MB',
        ];
    }

    public function getRecentlyDocumentsProperty(): LengthAwarePaginator
    {
        $user = auth()->user();

        assert($user instanceof User);

        return $user->documents()
            ->latest()
            ->paginate(6, ['id', 'name', 'code', 'created_at']);
    }

    public function submit(ExtractPaymentCodeUseCase $extractPaymentCodeUseCase): void
    {
        $this->errors = [];
        $this->extractedPaymentCodes = [];

        $this->validate();

        foreach ($this->files as $file) {
            $input = new Input(
                file: new File(name: $file->getClientOriginalName(), path: $file->getRealPath()),
                paymentCodeOwner: new PaymentCodeOwner(id: auth()->id())
            );

            try {
                $output = $extractPaymentCodeUseCase->execute($input);

                $this->extractedPaymentCodes[] = [
                    'name' => $file->getClientOriginalName(),
                    'code' => $output->paymentCode->code,
                ];
            } catch (FailedToExtractPaymentCodeException $e) {
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
