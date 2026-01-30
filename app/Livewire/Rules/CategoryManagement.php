<?php

namespace App\Livewire\Rules;

use App\Models\RuleCategory;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editingId = null;
    
    // Form fields
    public $name = '';
    public $description = '';
    public $display_order = 0;
    public $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'display_order' => 'required|integer|min:0',
        'is_active' => 'boolean'
    ];

    protected $messages = [
        'name.required' => 'نام دسته بندی الزامی است.',
        'name.max' => 'نام دسته بندی نباید بیشتر از ۲۵۵ کاراکتر باشد.',
        'display_order.required' => 'ترتیب نمایش الزامی است.',
        'display_order.min' => 'ترتیب نمایش نباید منفی باشد.'
    ];

    public function mount()
    {
        $this->loadCategories();
    }

    public function loadCategories()
    {
        // This will be called in render method
    }

    public function render()
    {
        $categories = RuleCategory::when($this->search, function($query) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
        })
        ->orderBy('display_order')
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        return view('livewire.rules.category-management', [
            'categories' => $categories
        ]);
    }

    public function openModal($id = null)
    {
        $this->resetValidation();
        $this->showModal = true;
        $this->editingId = $id;

        if ($id) {
            $category = RuleCategory::find($id);
            $this->name = $category->name;
            $this->description = $category->description ?? '';
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
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->editingId) {
                // Update existing category
                $category = RuleCategory::find($this->editingId);
                $category->update([
                    'name' => $this->name,
                    'description' => $this->description,
                    'display_order' => $this->display_order,
                    'is_active' => $this->is_active
                ]);
                
                session()->flash('success', 'دسته بندی با موفقیت ویرایش شد.');
            } else {
                // Create new category
                RuleCategory::create([
                    'name' => $this->name,
                    'description' => $this->description,
                    'display_order' => $this->display_order,
                    'is_active' => $this->is_active
                ]);
                
                session()->flash('success', 'دسته بندی با موفقیت ایجاد شد.');
            }

            $this->closeModal();
            
        } catch (\Exception $e) {
            session()->flash('error', 'خطا در ذخیره دسته بندی: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $category = RuleCategory::find($id);
            
            // Check if category has rules
            if ($category->rules()->count() > 0) {
                session()->flash('error', 'این دسته بندی دارای قوانین است و قابل حذف نیست.');
                return;
            }
            
            $category->delete();
            session()->flash('success', 'دسته بندی با موفقیت حذف شد.');
            
        } catch (\Exception $e) {
            session()->flash('error', 'خطا در حذف دسته بندی: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        try {
            $category = RuleCategory::find($id);
            $category->is_active = !$category->is_active;
            $category->save();
            
            $status = $category->is_active ? 'فعال' : 'غیرفعال';
            session()->flash('success', "دسته بندی با موفقیت {$status} شد.");
            
        } catch (\Exception $e) {
            session()->flash('error', 'خطا در تغییر وضعیت: ' . $e->getMessage());
        }
    }

    private function resetFields()
    {
        $this->name = '';
        $this->description = '';
        $this->display_order = 0;
        $this->is_active = true;
        $this->editingId = null;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
