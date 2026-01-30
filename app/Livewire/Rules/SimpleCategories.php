<?php

namespace App\Livewire\Rules;

use App\Models\RuleCategory;
use Livewire\Component;

class SimpleCategories extends Component
{
    public $categories;
    public $name = '';
    public $showForm = false;

    public function mount()
    {
        $this->loadCategories();
    }

    public function loadCategories()
    {
        $this->categories = RuleCategory::all();
    }

    public function render()
    {
        return view('livewire.rules.simple-categories');
    }

    public function showForm()
    {
        $this->showForm = true;
    }

    public function hideForm()
    {
        $this->showForm = false;
        $this->name = '';
    }

    public function save()
    {
        $this->validate(['name' => 'required|string|max:255']);
        
        RuleCategory::create([
            'name' => $this->name,
            'description' => '',
            'display_order' => 0,
            'is_active' => true
        ]);

        $this->loadCategories();
        $this->hideForm();
        session()->flash('message', 'دسته بندی با موفقیت ایجاد شد.');
    }

    public function delete($id)
    {
        RuleCategory::find($id)->delete();
        $this->loadCategories();
        session()->flash('message', 'دسته بندی با موفقیت حذف شد.');
    }
}
