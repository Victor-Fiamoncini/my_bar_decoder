<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="w-full space-y-6 mb-6">
        <flux:heading size="lg">{{ __('Bar Decoder') }}</flux:heading>

        <flux:subheading>{{ __('Upload a bill PDF document and we will extract its payment code.') }}</flux:subheadinga>

        <form class="space-y-2" wire:submit="submit">
            <div class="flex items-center justify-start">
                <flux:icon.document-arrow-up />

                <flux:input.file
                    class="block w-full text-md cursor-pointer p-3 rounded-md"
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
            >
                {{ __('Extract Payment Code') }}
            </flux:button>

            <flux:error name="file" />
        </form>

        @if ($paymentCode)
            <div class="p-6 border border-green-200 rounded-lg w-full" data-test="payment-code-card">
                <flux:heading>{{ __('Extracted Payment Code') }}</flux:heading>

                <flux:subheading>{{ $paymentCode }}</flux:subheading>
            </div>
        @endif
    </div>

    @if ($documents->isNotEmpty())
        <div class="space-y-6 w-full">
            <flux:heading size="lg">{{ __('Your Previous Documents') }}</flux:heading>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($documents as $document)
                    <div class="p-6 border rounded-lg w-full">
                        <div class="space-y-2">
                            <flux:heading size="sm">{{ $document->name }}</flux:heading>

                            <flux:subheading class="text-sm">{{ $document->code }}</flux:subheading>

                            <flux:badge size="sm" variant="subtle">
                                {{ $document->created_at->format('M d, Y H:i') }}
                            </flux:badge>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
