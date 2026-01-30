<?php

namespace App\Livewire\Rules;

use Livewire\Component;

class TestPage extends Component
{
    public $message = 'Hello World';
    public $count = 0;

    public function increment()
    {
        $this->count++;
    }

    public function showMessage()
    {
        session()->flash('success', 'دکمه کار می‌کند!');
    }

    public function render()
    {
        return view('livewire.rules.test-page');
    }
}
