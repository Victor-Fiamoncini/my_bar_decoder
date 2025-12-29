<div class="flex h-full w-full flex-1 flex-col gap-4">
    <div class="w-full space-y-6 mb-6">
        <flux:heading size="lg">{{ __('Bar Decoder') }}</flux:heading>

        <flux:subheading>{{ __('Upload a bill PDF document and we will extract its payment code.') }}</flux:subheadinga>

        <form class="space-y-2" wire:submit="submit">
            <div class="flex items-center justify-start">
                <flux:icon.document-arrow-up />

                <flux:input.file
                    class="block text-md cursor-pointer p-3 rounded-md"
                    wire:model="file"
                    type="file"
                    accept="image/png,image/jpeg,application/pdf"
                    data-test="file-input"
                />
            </div>

            <flux:button
                class="cursor-pointer"
                type="submit"
                variant="primary"
                data-test="submit-button"
                :disabled="!$file"
                wire:loading.attr="disabled"
                wire:target="file, submit"
            >
                <span wire:loading.remove wire:target="file, submit">{{ __('Extract Payment Code') }}</span>

                <span wire:loading wire:target="file">{{ __('Uploading...') }}</span>

                <span wire:loading wire:target="submit">{{ __('Extracting...') }}</span>
            </flux:button>

            <flux:error name="file" />
        </form>

        @if ($paymentCode)
            <div class="p-6 border border-green-200 rounded-lg space-y-2" data-test="payment-code-card">
                <flux:heading>{{ __('Extracted Payment Code') }}</flux:heading>

                <div class="flex items-center justify-start gap-2 flex-wrap">
                    <flux:button
                        class="cursor-pointer"
                        type="button"
                        size="sm"
                        variant="primary"
                        icon="clipboard"
                        onclick="navigator.clipboard.writeText('{{ $paymentCode }}');"
                        title="{{ __('Copy to clipboard') }}"
                        data-test="copy-button"
                    >
                        {{ __('Copy') }}
                    </flux:button>

                    <flux:subheading class="break-all">{{ $paymentCode }}</flux:subheading>
                </div>
            </div>
        @endif
    </div>

    @if ($this->documents->isNotEmpty())
        <div class="space-y-6 w-full">
            <flux:heading size="lg">{{ __('Your Previous Documents') }}</flux:heading>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($this->documents as $document)
                    <div class="p-6 border rounded-lg w-full">
                        <div class="space-y-2" data-test="document-card">
                            <flux:heading class="wrap-break-word">{{ $document->name }}</flux:heading>

                            <flux:subheading class="break-all">{{ $document->code }}</flux:subheading>

                            <flux:badge size="sm" variant="subtle">
                                {{ $document->created_at->format('M d, Y H:i') }}
                            </flux:badge>
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($this->documents->hasPages())
                <div class="flex items-center justify-between">
                    <div class="flex gap-2">
                        @if ($this->documents->onFirstPage())
                            <flux:button class="cursor-pointer" disabled>
                                {{ __('Previous') }}
                            </flux:button>
                        @else
                            <flux:button class="cursor-pointer" wire:click="previousPage">
                                {{ __('Previous') }}
                            </flux:button>
                        @endif

                        @if ($this->documents->hasMorePages())
                            <flux:button class="cursor-pointer" wire:click="nextPage">
                                {{ __('Next') }}
                            </flux:button>
                        @else
                            <flux:button class="cursor-pointer" disabled>
                                {{ __('Next') }}
                            </flux:button>
                        @endif
                    </div>

                    <flux:subheading>
                        {{ __('Page :current of :last', [
                            'current' => $this->documents->currentPage(),
                            'last' => $this->documents->lastPage()
                        ]) }}
                    </flux:subheading>
                </div>
            @endif
        </div>
    @endif
</div>
