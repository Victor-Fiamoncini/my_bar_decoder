<flux:button
    class="cursor-pointer"
    type="button"
    size="sm"
    icon="clipboard"
    title="{{ __('Copy to clipboard') }}"
    wire:click="copy"
    data-test="clipboard-button"
>
    Copy
</flux:button>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('copy-to-clipboard', ({ text }) => navigator.clipboard.writeText(text));
    });
</script>
