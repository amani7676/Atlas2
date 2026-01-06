<?php

namespace App\Livewire\Pages\Keys;

use Livewire\Component;
use App\Models\Key;
use App\Models\Room;
use App\Models\Unit;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

class KeyRoomTable extends Component
{
    use WithPagination;

    // Properties for Filtering and Search
    public $search = '';
    public $selectedUnit = '';
    public $selectedType = '';

    // Properties for Key Modal
    public $showKeyModal = false;
    public $keyId;
    public $keyName, $keyCode, $keyDesc, $keyNote;

    // Properties for Assignment Modal
    public $showAssignmentModal = false;
    public $assignmentKeyId;
    public $assignmentRoomId;
    public $assignmentNotes;
    public $assignmentExpiresAt;
    public $allFilteredRooms = []; // For the dropdown in the assignment modal
    public $isEditingAssignment = false;


    protected $queryString = [
        'search' => ['except' => ''],
        'selectedUnit' => ['except' => ''],
        'selectedType' => ['except' => ''],
    ];

    /**
     * Validation rules.
     */
    protected function rules()
    {
        return [
            'keyName' => ['required', 'string', 'max:255', Rule::unique('keys', 'name')->ignore($this->keyId)],
            'keyCode' => 'required|string|max:255',
            'keyDesc' => 'nullable|string',
            'keyNote' => 'nullable|string',
            'assignmentRoomId' => 'required_if:isEditingAssignment,false|exists:rooms,id',
            'assignmentNotes' => 'nullable|string',
            'assignmentExpiresAt' => 'nullable|date',
        ];
    }

    /**
     * Reset pagination when searching or filtering.
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedUnit()
    {
        $this->resetPage();
    }

    public function updatingSelectedType()
    {
        $this->resetPage();
    }

    //##############################
    //## Key Modal Methods
    //##############################

    public function prepareKeyCreate()
    {
        $this->resetKeyForm();
        $this->showKeyModal = true;
    }

    public function prepareKeyEdit($keyId)
    {
        $key = Key::findOrFail($keyId);
        $this->resetKeyForm();
        $this->keyId = $key->id;
        $this->keyName = $key->name;
        $this->keyCode = $key->code;
        $this->keyDesc = $key->desc;
        $this->keyNote = $key->note;
        $this->showKeyModal = true;
    }

    public function saveKey()
    {
        $data = $this->validate([
            'keyName' => ['required', 'string', 'max:255', Rule::unique('keys', 'name')->ignore($this->keyId)],
            'keyCode' => 'required|string|max:255',
            'keyDesc' => 'nullable|string',
            'keyNote' => 'nullable|string',
        ]);

        // Use a more concise way to name fields
        $keyData = [
            'name' => $data['keyName'],
            'code' => $data['keyCode'],
            'desc' => $data['keyDesc'] ?? null,
            'note' => $data['keyNote'] ?? null,
        ];

        if ($this->keyId) {
            $key = Key::findOrFail($this->keyId);
            $key->update($keyData);
            $message = 'کلید با موفقیت ویرایش شد.';
        } else {
            Key::create($keyData);
            $message = 'کلید با موفقیت ایجاد شد.';
        }

        $this->resetKeyForm();
        $this->showKeyModal = false;
        
        $this->js("
            if (typeof cuteToast === 'function') {
                cuteToast({
                    type: 'success',
                    title: 'موفقیت!',
                    description: '{$message}',
                    timer: 5000
                });
            }
        ");
    }

    public function resetKeyForm()
    {
        $this->reset(['keyId', 'keyName', 'keyCode', 'keyDesc', 'keyNote']);
    }


    //##############################
    //## Assignment Modal Methods
    //##############################

    public function prepareAssignmentCreate(Key $key)
    {
        $this->resetAssignmentForm();
        $this->assignmentKeyId = $key->id;
        $this->isEditingAssignment = false;

        // Load rooms that are not already assigned to this key
        $assignedRoomIds = $key->rooms()->pluck('rooms.id');
        $this->allFilteredRooms = Room::query()
            ->when($this->selectedUnit, fn($q) => $q->where('unit_id', $this->selectedUnit))
            ->when($this->selectedType, fn($q) => $q->where('type', $this->selectedType))
            ->whereNotIn('id', $assignedRoomIds)
            ->orderBy('name')
            ->get();

        $this->showAssignmentModal = true;
    }

    public function prepareAssignmentEdit($keyId, $roomId)
    {
        $this->resetAssignmentForm();
        $key = Key::with(['rooms' => fn($q) => $q->where('room_id', $roomId)])->findOrFail($keyId);
        $room = $key->rooms->first();

        if ($room) {
            $this->assignmentKeyId = $key->id;
            $this->assignmentRoomId = $room->id;
            $this->assignmentNotes = $room->pivot->notes;
            $this->assignmentExpiresAt = $room->pivot->expires_at ? \Carbon\Carbon::parse($room->pivot->expires_at)->format('Y-m-d\TH:i') : null;
            $this->isEditingAssignment = true;
            $this->showAssignmentModal = true;
        }
    }

    public function saveAssignment()
    {
        // اگر assignmentRoomId آرایه باشد، آن را به آرایه تبدیل کنید
        $roomIds = is_array($this->assignmentRoomId) ? $this->assignmentRoomId : [$this->assignmentRoomId];

        // اعتبارسنجی
        $rules = [
            'assignmentRoomId' => 'required|array', // اطمینان از آرایه بودن
            'assignmentRoomId.*' => 'exists:rooms,id', // اعتبارسنجی هر ID اتاق
        ];

        // اگر فقط یک اتاق انتخاب شده، فیلدهای notes و expires_at الزامی هستند
        if (count($roomIds) === 1) {
            $rules['assignmentNotes'] = 'nullable|string';
            $rules['assignmentExpiresAt'] = 'nullable|date';
        } else {
            $rules['assignmentNotes'] = 'nullable|string';
            $rules['assignmentExpiresAt'] = 'nullable|date';
        }

        $this->validate($rules);

        // پیدا کردن کلید
        $key = Key::findOrFail($this->assignmentKeyId);

        // آماده‌سازی داده‌ها برای sync
        $syncData = [];
        foreach ($roomIds as $roomId) {
            $syncData[$roomId] = [
                'notes' => count($roomIds) === 1 ? $this->assignmentNotes : null,
                'expires_at' => count($roomIds) === 1 ? $this->assignmentExpiresAt : null,
            ];
        }

        // به‌روزرسانی روابط
        $key->rooms()->syncWithoutDetaching($syncData);



        // بستن مودال
        $this->showAssignmentModal = false;


        $this->js("
                cuteToast({
                    type: 'success',
                    title: 'Added!',
                    description: 'اضافه شدن کلید ',
                    timer: 5000
                })
            ");
    }

    public function removeAssignment($keyId = null, $roomId = null)
    {
        if ($keyId && $roomId) {
            $key = Key::findOrFail($keyId);
            $key->rooms()->detach($roomId);
        } else {
            $key = Key::findOrFail($this->assignmentKeyId);
            $key->rooms()->detach($this->assignmentRoomId);
        }

        $this->js("
                cuteToast({
                    type: 'error',
                    title: 'Deleted!',
                    description: 'حذف تخصیص ',
                    timer: 5000
                })
            ");
        $this->showAssignmentModal = false;
    }

    public function resetAssignmentForm()
    {
        $this->reset(['assignmentKeyId', 'assignmentRoomId', 'assignmentNotes', 'assignmentExpiresAt', 'isEditingAssignment']);
    }


    /** delete keys */
    public function confirmRemoveKey($keyId): void
    {
        $this->dispatch('confirm-delete-key', keyId: $keyId);
    }

    public function deleteKey($keyId): void
    {
        try {
            $key = Key::findOrFail($keyId);
            $keyName = $key->name;

            $key->rooms()->detach();
            $key->delete();

            $this->js("
                cuteToast({
                    type: 'error',
                    title: 'Deleted!',
                    description: 'حذف کلید ',
                    timer: 5000
                })
            ");

            $this->resetPage();
        } catch (\Exception $e) {
            $this->js("
                cuteToast({
                    type: 'error',
                    title: 'Deleted!',
                    description: 'حذف کلید با خطا مواجه شد!  ',
                    timer: 5000
                })
            ");
        }
    }

    /**
     * Render the component.
     */
    public function render()
    {
        // First, get the IDs of rooms that match the filters
        $filteredRoomIds = Room::query()
            ->when($this->selectedUnit, fn($q) => $q->where('unit_id', $this->selectedUnit))
            ->when($this->selectedType, fn($q) => $q->where('type', $this->selectedType))
            ->pluck('id');

        // Now, get the keys and eager-load only the rooms that match our filtered IDs
        $keysQuery = Key::with(['rooms' => function ($query) use ($filteredRoomIds) {
            $query->whereIn('rooms.id', $filteredRoomIds)->select('rooms.id', 'rooms.name');
        }])->orderBy('code');

        if ($this->search) {
            $keysQuery->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%')
                    ->orWhere('note', 'like', '%' . $this->search . '%')
                    ->orWhereHas('rooms', function ($query) {
                        $query->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        $keys = $keysQuery->paginate(30);

        // Get units for the filter dropdown
        $units = Unit::orderBy('name')->get();

        return view('livewire.pages.keys.key-room-table', compact('keys', 'units'))
            ->title('مدیریت کلیدها');
    }
}
