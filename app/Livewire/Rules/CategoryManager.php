<?php

namespace App\Livewire\Rules;

use App\Models\RuleCategory;
use Livewire\Component;

class CategoryManager extends Component
{
    public $categories;
    public $showModal = false;
    public $editingId = null;
    public $name = '';
    public $description = '';
    public $display_order = 0;
    public $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'display_order' => 'required|integer|min:0'
    ];

    public function mount()
    {
        $this->loadCategories();
    }

    public function loadCategories()
    {
        $this->categories = RuleCategory::orderBy('display_order')->get();
    }

    public function render()
    {
        return view('livewire.rules.category-manager');
    }

    public function openModal($id = null)
    {
        $this->showModal = true;
        $this->editingId = $id;

        if ($id) {
            $category = RuleCategory::find($id);
            $this->name = $category->name;
            $this->description = $category->description;
            $this->display_order = $category->display_order;
            $this->is_active = $category->is_active;
        } else {
            $this->resetFields();
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetFields();
    }

    public function save()
    {
        $this->validate();

        if ($this->editingId) {
            RuleCategory::find($this->editingId)->update([
                'name' => $this->name,
                'description' => $this->description,
                'display_order' => $this->display_order,
                'is_active' => $this->is_active
            ]);
        } else {
            RuleCategory::create([
                'name' => $this->name,
                'description' => $this->description,
                'display_order' => $this->display_order,
                'is_active' => $this->is_active
            ]);
        }

        $this->loadCategories();
        $this->closeModal();
        session()->flash('message', 'دسته بندی با موفقیت ذخیره شد.');
    }

    public function delete($id)
    {
        $category = RuleCategory::find($id);
        if ($category->rules()->count() > 0) {
            session()->flash('error', 'این دسته بندی دارای قوانین است و قابل حذف نیست.');
            return;
        }
        $category->delete();
        $this->loadCategories();
        session()->flash('message', 'دسته بندی با موفقیت حذف شد.');
    }

    public function toggleStatus($id)
    {
        $category = RuleCategory::find($id);
        $category->is_active = !$category->is_active;
        $category->save();
        $this->loadCategories();
    }

    private function resetFields()
    {
        $this->name = '';
        $this->description = '';
        $this->display_order = 0;
        $this->is_active = true;
        $this->editingId = null;
    }
}
