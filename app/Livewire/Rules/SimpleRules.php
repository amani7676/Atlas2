<?php

namespace App\Livewire\Rules;

use App\Models\Rule;
use App\Models\RuleCategory;
use Livewire\Component;
use Livewire\WithPagination;

class SimpleRules extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedCategory = null;
    public $showModal = false;
    public $editingId = null;
    
    // Form fields
    public $title = '';
    public $content = '';
    public $display_order = 0;
    public $is_active = true;
    public $rule_category_id = null;

    protected $rules = [
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'display_order' => 'required|integer|min:0',
        'rule_category_id' => 'required|exists:rule_categories,id'
    ];

    protected $messages = [
        'title.required' => 'عنوان قانون الزامی است.',
        'content.required' => 'محتوای قانون الزامی است.',
        'rule_category_id.required' => 'انتخاب دسته بندی الزامی است.',
        'display_order.required' => 'ترتیب نمایش الزامی است.'
    ];

    public function mount()
    {
        $this->loadCategories();
    }

    public function loadCategories()
    {
        // Categories will be loaded in render
    }

    public function render()
    {
        $categories = RuleCategory::where('is_active', true)->orderBy('display_order')->get();
        
        $rules = Rule::with('category')
            ->when($this->selectedCategory, function($query) {
                $query->where('rule_category_id', $this->selectedCategory);
            })
            ->when($this->search, function($query) {
                $query->where('title', 'like', '%' . $this->search . '%');
            })
            ->orderBy('display_order')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.rules.simple-rules', [
            'categories' => $categories,
            'rules' => $rules
        ]);
    }

    public function openModal($id = null)
    {
        $this->resetValidation();
        $this->showModal = true;
        $this->editingId = $id;

        if ($id) {
            $rule = Rule::find($id);
            $this->title = $rule->title;
            $this->content = $rule->content;
            $this->display_order = $rule->display_order;
            $this->is_active = $rule->is_active;
            $this->rule_category_id = $rule->rule_category_id;
        } else {
            $this->resetFields();
            $this->rule_category_id = $this->selectedCategory;
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
                // Update existing rule
                $rule = Rule::find($this->editingId);
                $rule->update([
                    'title' => $this->title,
                    'content' => $this->content,
                    'display_order' => $this->display_order,
                    'is_active' => $this->is_active,
                    'rule_category_id' => $this->rule_category_id
                ]);
                
                session()->flash('success', 'قانون با موفقیت ویرایش شد.');
            } else {
                // Create new rule
                Rule::create([
                    'title' => $this->title,
                    'content' => $this->content,
                    'display_order' => $this->display_order,
                    'is_active' => $this->is_active,
                    'rule_category_id' => $this->rule_category_id
                ]);
                
                session()->flash('success', 'قانون با موفقیت ایجاد شد.');
            }

            $this->closeModal();
            
        } catch (\Exception $e) {
            session()->flash('error', 'خطا در ذخیره قانون: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            Rule::find($id)->delete();
            session()->flash('success', 'قانون با موفقیت حذف شد.');
            
        } catch (\Exception $e) {
            session()->flash('error', 'خطا در حذف قانون: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        try {
            $rule = Rule::find($id);
            $rule->is_active = !$rule->is_active;
            $rule->save();
            
            $status = $rule->is_active ? 'فعال' : 'غیرفعال';
            session()->flash('success', "قانون با موفقیت {$status} شد.");
            
        } catch (\Exception $e) {
            session()->flash('error', 'خطا در تغییر وضعیت: ' . $e->getMessage());
        }
    }

    public function filterByCategory($categoryId)
    {
        $this->selectedCategory = $categoryId;
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->selectedCategory = null;
        $this->search = '';
        $this->resetPage();
    }

    private function resetFields()
    {
        $this->title = '';
        $this->content = '';
        $this->display_order = 0;
        $this->is_active = true;
        $this->rule_category_id = null;
        $this->editingId = null;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
