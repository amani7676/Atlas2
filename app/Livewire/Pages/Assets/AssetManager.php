<?php

namespace App\Livewire\Pages\Assets;

use App\Models\AssetType;
use Livewire\Component;

class AssetManager extends Component
{
    public $assetTypes;

    // Form properties for Asset Type
    public $assetTypeName = '';
    public $assetTypeIcon = '';
    public $assetTypeDescription = '';
    public $assetTypeIsActive = true;

    // Modal states
    public $showAssetTypeModal = false;
    public $editingAssetTypeId = null;

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->assetTypes = AssetType::active()->get();
    }

    // Asset Type CRUD Methods
    public function openAssetTypeModal($assetTypeId = null)
    {
        $this->resetAssetTypeForm();
        if ($assetTypeId) {
            $assetType = AssetType::findOrFail($assetTypeId);
            $this->editingAssetTypeId = $assetTypeId;
            $this->assetTypeName = $assetType->name;
            $this->assetTypeIcon = $assetType->icon;
            $this->assetTypeDescription = $assetType->description;
            $this->assetTypeIsActive = $assetType->is_active;
        } else {
            $this->editingAssetTypeId = null;
        }
        $this->showAssetTypeModal = true;
    }

    public function saveAssetType()
    {
        $rules = [
            'assetTypeName' => 'required|string|max:255',
            'assetTypeIcon' => 'nullable|string|max:50',
            'assetTypeDescription' => 'nullable|string',
            'assetTypeIsActive' => 'boolean',
        ];

        $this->validate($rules);

        try {
            $data = [
                'name' => $this->assetTypeName,
                'icon' => $this->assetTypeIcon ?: null,
                'description' => $this->assetTypeDescription,
                'is_active' => $this->assetTypeIsActive,
            ];

            if ($this->editingAssetTypeId) {
                AssetType::findOrFail($this->editingAssetTypeId)->update($data);
                $this->dispatch('show-toast', [
                    'type' => 'info',
                    'title' => 'به‌روزرسانی شد!',
                    'description' => 'نوع دارایی با موفقیت به‌روزرسانی شد',
                    'timer' => 3000
                ]);
            } else {
                AssetType::create($data);
                $this->dispatch('show-toast', [
                    'type' => 'success',
                    'title' => 'ایجاد شد!',
                    'description' => 'نوع دارایی جدید با موفقیت ایجاد شد',
                    'timer' => 3000
                ]);
            }

            $this->closeAssetTypeModal();
            $this->loadData();
        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'title' => 'خطا!',
                'description' => 'خطایی رخ داد: ' . $e->getMessage(),
                'timer' => 3000
            ]);
        }
    }

    public function closeAssetTypeModal()
    {
        $this->showAssetTypeModal = false;
        $this->resetAssetTypeForm();
    }

    public function resetAssetTypeForm()
    {
        $this->editingAssetTypeId = null;
        $this->assetTypeName = '';
        $this->assetTypeIcon = '';
        $this->assetTypeDescription = '';
        $this->assetTypeIsActive = true;
    }

    public function deleteAssetType($assetTypeId)
    {
        try {
            $assetType = AssetType::findOrFail($assetTypeId);
            $assetType->delete();

            $this->dispatch('show-toast', [
                'type' => 'error',
                'title' => 'حذف شد!',
                'description' => 'نوع دارایی با موفقیت حذف شد',
                'timer' => 3000
            ]);

            $this->loadData();
        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'title' => 'خطا!',
                'description' => 'خطا در حذف نوع دارایی: ' . $e->getMessage(),
                'timer' => 3000
            ]);
        }
    }

    public function render()
    {
        return view('livewire.pages.assets.asset-manager')
            ->title('مدیریت انواع دارایی‌ها');
    }
}
