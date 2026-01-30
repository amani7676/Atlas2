<?php

namespace App\Livewire\Rules;

use App\Models\Rule;
use App\Models\RuleCategory;
use Livewire\Component;
use Livewire\WithPagination;

class RulesManager extends Component
{
    use WithPagination;

    public $categories;
    public $selectedCategory = null;
    public $search = '';
    public $showCategoryModal = false;
    public $showRuleModal = false;
    public $editingCategory = null;
    public $editingRule = null;

    // Category fields
    public $categoryName = '';
    public $categoryDescription = '';
    public $categoryDisplayOrder = 0;
    public $categoryIsActive = true;

    // Rule fields
    public $ruleTitle = '';
    public $ruleContent = '';
    public $ruleDisplayOrder = 0;
    public $ruleIsActive = true;
    public $ruleCategoryId = null;

    protected $rules = [
        'categoryName' => 'required|string|max:255',
        'categoryDescription' => 'nullable|string',
        'categoryDisplayOrder' => 'required|integer|min:0',
        'ruleTitle' => 'required|string|max:255',
        'ruleContent' => 'required|string',
        'ruleDisplayOrder' => 'required|integer|min:0',
        'ruleCategoryId' => 'required|exists:rule_categories,id'
    ];

    public function mount()
    {
        $this->loadCategories();
    }

    public function loadCategories()
    {
        $this->categories = RuleCategory::active()->ordered()->get();
    }

    public function render()
    {
        $rules = Rule::with('category')
            ->when($this->selectedCategory, function ($query) {
                $query->where('rule_category_id', $this->selectedCategory);
            })
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%');
            })
            ->active()
            ->ordered()
            ->paginate(10);

        return view('livewire.rules.rules-manager', [
            'rules' => $rules
        ]);
    }

    // Category Methods
    public function openCategoryModal($categoryId = null)
    {
        $this->showCategoryModal = true;
        $this->editingCategory = $categoryId;

        if ($categoryId) {
            $category = RuleCategory::find($categoryId);
            $this->categoryName = $category->name;
            $this->categoryDescription = $category->description;
            $this->categoryDisplayOrder = $category->display_order;
            $this->categoryIsActive = $category->is_active;
        } else {
            $this->resetCategoryFields();
        }
    }

    public function closeCategoryModal()
    {
        $this->showCategoryModal = false;
        $this->resetCategoryFields();
    }

    public function testSave()
    {
        session()->flash('message', 'تست: Livewire کار می‌کند!');
    }

    public function saveCategory()
    {
        $this->validate([
            'categoryName' => 'required|string|max:255',
            'categoryDescription' => 'nullable|string',
            'categoryDisplayOrder' => 'required|integer|min:0'
        ]);

        if ($this->editingCategory) {
            RuleCategory::find($this->editingCategory)->update([
                'name' => $this->categoryName,
                'description' => $this->categoryDescription,
                'display_order' => $this->categoryDisplayOrder,
                'is_active' => $this->categoryIsActive
            ]);
        } else {
            RuleCategory::create([
                'name' => $this->categoryName,
                'description' => $this->categoryDescription,
                'display_order' => $this->categoryDisplayOrder,
                'is_active' => $this->categoryIsActive
            ]);
        }

        $this->loadCategories();
        $this->closeCategoryModal();
        session()->flash('message', 'دسته بندی با موفقیت ذخیره شد.');
    }

    public function deleteCategory($categoryId)
    {
        $category = RuleCategory::find($categoryId);
        if ($category->rules()->count() > 0) {
            session()->flash('error', 'این دسته بندی دارای قوانین است و قابل حذف نیست.');
            return;
        }

        $category->delete();
        $this->loadCategories();
        session()->flash('message', 'دسته بندی با موفقیت حذف شد.');
    }

    // Rule Methods
    public function openRuleModal($ruleId = null)
    {
        $this->showRuleModal = true;
        $this->editingRule = $ruleId;

        if ($ruleId) {
            $rule = Rule::find($ruleId);
            $this->ruleTitle = $rule->title;
            $this->ruleContent = $rule->content;
            $this->ruleDisplayOrder = $rule->display_order;
            $this->ruleIsActive = $rule->is_active;
            $this->ruleCategoryId = $rule->rule_category_id;
        } else {
            $this->resetRuleFields();
            $this->ruleCategoryId = $this->selectedCategory;
        }
    }

    public function closeRuleModal()
    {
        $this->showRuleModal = false;
        $this->resetRuleFields();
    }

    public function saveRule()
    {
        $this->validate([
            'ruleTitle' => 'required|string|max:255',
            'ruleContent' => 'required|string',
            'ruleDisplayOrder' => 'required|integer|min:0',
            'ruleCategoryId' => 'required|exists:rule_categories,id'
        ]);

        if ($this->editingRule) {
            Rule::find($this->editingRule)->update([
                'title' => $this->ruleTitle,
                'content' => $this->ruleContent,
                'display_order' => $this->ruleDisplayOrder,
                'is_active' => $this->ruleIsActive,
                'rule_category_id' => $this->ruleCategoryId
            ]);
        } else {
            Rule::create([
                'title' => $this->ruleTitle,
                'content' => $this->ruleContent,
                'display_order' => $this->ruleDisplayOrder,
                'is_active' => $this->ruleIsActive,
                'rule_category_id' => $this->ruleCategoryId
            ]);
        }

        $this->closeRuleModal();
        session()->flash('message', 'قانون با موفقیت ذخیره شد.');
    }

    public function deleteRule($ruleId)
    {
        Rule::find($ruleId)->delete();
        session()->flash('message', 'قانون با موفقیت حذف شد.');
    }

    public function toggleRuleStatus($ruleId)
    {
        $rule = Rule::find($ruleId);
        $rule->is_active = !$rule->is_active;
        $rule->save();
    }

    public function toggleCategoryStatus($categoryId)
    {
        $category = RuleCategory::find($categoryId);
        $category->is_active = !$category->is_active;
        $category->save();
        $this->loadCategories();
    }

    // Helper Methods
    private function resetCategoryFields()
    {
        $this->categoryName = '';
        $this->categoryDescription = '';
        $this->categoryDisplayOrder = 0;
        $this->categoryIsActive = true;
        $this->editingCategory = null;
    }

    private function resetRuleFields()
    {
        $this->ruleTitle = '';
        $this->ruleContent = '';
        $this->ruleDisplayOrder = 0;
        $this->ruleIsActive = true;
        $this->ruleCategoryId = null;
        $this->editingRule = null;
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
}
