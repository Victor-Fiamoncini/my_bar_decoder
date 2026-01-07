<div class="flex h-full w-full flex-1 flex-col gap-4">
    <div class="w-full space-y-6 mb-6">
        <flux:heading size="xl">{{ __('Bar Decoder') }}</flux:heading>

        <flux:subheading size="lg">
            {{ __('Upload a bill PDF document and we will extract its payment code') }}
        </flux:subheadinga>

        <flux:separator variant="subtle" />

        <form class="space-y-2" wire:submit="submit">
            <div class="flex items-center justify-start">
                <flux:icon.document-arrow-up />

                <flux:input.file
                    class="block text-md cursor-pointer p-3 rounded-md"
                    wire:model="files"
                    type="file"
                    accept="application/pdf"
                    data-test="file-input"
                    multiple="multiple"
                />
            </div>

            <flux:button
                type="submit"
                variant="primary"
                data-test="submit-button"
                :disabled="is_array($files) && count($files) === 0"
                wire:loading.attr="disabled"
                wire:target="files, submit"
            >
                <span wire:loading.remove wire:target="files, submit">{{ __('Extract Payment Code') }}</span>

                <span wire:loading wire:target="files">{{ __('Uploading...') }}</span>

                <span wire:loading wire:target="submit">{{ __('Extracting...') }}</span>
            </flux:button>

            <flux:error name="files" />
        </form>

        @if (count($extractedPaymentCodes) > 0)
            <div class="space-y-4">
                <flux:heading>{{ __('Extracted Payment Codes') }}</flux:heading>

                @foreach ($extractedPaymentCodes as $extractedPaymentCode)
                    <div class="p-6 border border-primary rounded-lg space-y-2" data-test="payment-code-card">
                        <flux:heading size="sm">{{ $extractedPaymentCode['name'] }}</flux:heading>

                        <div class="flex items-center justify-start gap-2 flex-wrap">
                            <flux:button
                                size="sm"
                                variant="filled"
                                icon="clipboard"
                                onclick="navigator.clipboard.writeText('{{ $extractedPaymentCode['code'] }}');"
                                title="{{ __('Copy to clipboard') }}"
                                data-test="copy-button"
                            >
                                {{ __('Copy') }}
                            </flux:button>

                            <flux:subheading class="break-all">{{ $extractedPaymentCode['code'] }}</flux:subheading>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        @if (count($errors) > 0)
            <div class="space-y-4">
                <flux:heading>{{ __('Processing Errors') }}</flux:heading>

                @foreach ($errors as $error)
                    <div class="p-6 border border-red-600 rounded-lg space-y-2" data-test="error-card">
                        <flux:heading size="sm">{{ $error['name'] }}</flux:heading>

                        <flux:subheading class="text-red-600 break-all">{{ $error['message'] }}</flux:subheading>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @if ($this->recentlyDocuments->isNotEmpty())
        <div class="space-y-6 w-full">
            <flux:heading size="lg">{{ __('Your Previous Documents') }}</flux:heading>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($this->recentlyDocuments as $recentlyDocument)
                    <div class="p-6 border rounded-lg w-full">
                        <div class="space-y-2" data-test="document-card">
                            <flux:heading class="wrap-break-word">{{ $recentlyDocument->name }}</flux:heading>

                            <flux:subheading class="break-all">{{ $recentlyDocument->code }}</flux:subheading>

                            <flux:badge size="sm" variant="subtle">
                                {{ $recentlyDocument->created_at->format('M d, Y H:i') }}
                            </flux:badge>
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($this->recentlyDocuments->hasPages())
                <div class="flex items-center justify-between">
                    <div class="flex gap-2">
                        @if ($this->recentlyDocuments->onFirstPage())
                            <flux:button disabled>
                                {{ __('Previous') }}
                            </flux:button>
                        @else
                            <flux:button wire:click="previousPage">
                                {{ __('Previous') }}
                            </flux:button>
                        @endif

                        @if ($this->recentlyDocuments->hasMorePages())
                            <flux:button wire:click="nextPage">
                                {{ __('Next') }}
                            </flux:button>
                        @else
                            <flux:button disabled>
                                {{ __('Next') }}
                            </flux:button>
                        @endif
                    </div>

                    <flux:subheading>
                        {{ __('Page :current of :last', [
                            'current' => $this->recentlyDocuments->currentPage(),
                            'last' => $this->recentlyDocuments->lastPage()
                        ]) }}
                    </flux:subheading>
                </div>
            @endif
        </div>
    @endif
</div>
