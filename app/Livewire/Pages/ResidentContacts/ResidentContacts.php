<?php

namespace App\Livewire\Pages\ResidentContacts;

use App\Models\Resident;
use Livewire\Component;

class ResidentContacts extends Component
{
    public $residents = [];

    public function mount()
    {
        $this->loadResidents();
    }

    public function loadResidents()
    {
        $this->residents = Resident::where('phone', '!=', '')
            ->whereNotNull('phone')
            ->get();
    }

    public function isValidPhoneNumber($phone)
    {
        // Remove all non-digit characters
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        
        // Check if it's a valid Iranian mobile number (starts with 09 and has 11 digits)
        return preg_match('/^09[0-9]{9}$/', $cleanPhone);
    }

    public function exportToTxt()
    {
        $content = "";
        
        foreach ($this->residents as $resident) {
            $content .= $resident->phone . "\n";
        }
        
        $filename = 'اقامتگران.txt';
        
        $this->dispatch('download-file', [
            'content' => $content,
            'filename' => $filename
        ]);
    }

    public function render()
    {
        return view('livewire.pages.resident-contacts.resident-contacts')
            ->layout('components.layouts.app')
            ->title('شماره تماس اقامتگران');
    }
}
