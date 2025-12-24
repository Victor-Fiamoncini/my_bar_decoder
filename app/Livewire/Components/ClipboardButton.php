<?php

namespace App\Livewire\Components;

use Livewire\Component;

class ClipboardButton extends Component
{
    public string $text = '';

    public function copy(): void
    {
        $this->dispatch('copy-to-clipboard', text: $this->text);
    }

    public function render(): mixed
    {
        return view('livewire.components.clipboard-button');
    }
}
