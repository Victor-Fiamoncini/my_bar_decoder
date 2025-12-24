<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="max-w-lg w-full space-y-6">
        <flux:heading>{{ __('Bar Decoder') }}</flux:heading>

        <flux:subheading>{{ __('Upload a bill PDF document and we will extract its payment code.') }}</flux:subheading>

        <form class="space-y-2 mb-6" wire:submit="submit">
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
            <flux:heading size="md">{{ __('Extracted Payment Code') }}</flux:heading>

            <flux:subheading>{{ $paymentCode }}</flux:subheading>
        @endif
    </div>
</div>
