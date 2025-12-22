<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="max-w-lg w-full space-y-6">
        <flux:heading>{{ __('Bar Decoder') }}</flux:heading>

        <flux:subheading>{{ __('Upload a bill document/image and we will extract its barcode.') }}</flux:subheading>

        <form class="space-y-4" wire:submit="submit">
            <input
                class="block w-full cursor-pointer"
                type="file"
                wire:model="file"
                accept="image/png,image/jpeg,application/pdf"
            />

            <div class="flex items-center justify-end">
                <flux:button class="cursor-pointer" type="submit" variant="primary" data-test="decode-bill-button">
                    {{ __('Extract Barcode') }}
                </flux:button>
            </div>
        </form>

        @if ($barcode)
            <flux:heading size="lg">{{ __('Barcode') }}: {{ $barcode }}</flux:heading>
        @endif
    </div>

    <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
        <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
    </div>
</div>
