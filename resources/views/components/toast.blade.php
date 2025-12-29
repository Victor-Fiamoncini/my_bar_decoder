<div>
    <div
        class="fixed bottom-4 right-4 z-50 rounded-lg text-md p-4 shadow-lg"
        style="display: none;"
        x-data="{ show: false, message: '', type: '' }"
        x-on:toast.window="show = true; message = $event.detail.message; type = $event.detail.type; setTimeout(() => show = false, 3000);"
        x-show="show"
        :class="{
            'bg-lime-500 dark:bg-lime-600 text-white': type === 'success',
            'bg-red-500 dark:bg-red-600 text-white': type === 'error'
        }"
        x-transition
    >
        <p class="font-semibold" x-text="message"></p>
    </div>

    @if (session()->has('success'))
        <div x-init="$dispatch('toast', { message: '{{ session('success') }}', type: 'success' })" x-data></div>
    @endif

    @if (session()->has('error'))
        <div x-init="$dispatch('toast', { message: '{{ session('error') }}', type: 'error' })" x-data></div>
    @endif
</div>
