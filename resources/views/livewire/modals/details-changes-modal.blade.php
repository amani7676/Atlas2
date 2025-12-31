<div dir="rtl">
    <style>
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1050;
            animation: fadeIn 0.2s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes slideIn {
            from {
                transform: translate(-50%, -60%);
                opacity: 0;
            }
            to {
                transform: translate(-50%, -50%);
                opacity: 1;
            }
        }

        .modal-container {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            border-radius: 12px;
            max-width: 900px;
            width: 95%;
            max-height: 90vh;
            overflow: hidden;
            z-index: 1051;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
            animation: slideIn 0.3s ease-out;
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 12px 12px 0 0;
        }

        .modal-body {
            max-height: 60vh;
            overflow-y: auto;
            padding: 1.5rem;
            background-color: #f8f9fa;
        }

        .collapse-toggle {
            background: linear-gradient(135deg, #6c5ce7 0%, #a29bfe 100%);
            border: none;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            text-align: right;
            padding: 1.2rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-radius: 8px 8px 0 0;
        }

        .collapse-toggle:hover {
            background: linear-gradient(135deg, #5f3dc4 0%, #9775fa 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .collapse-toggle.collapsed {
            background: linear-gradient(135deg, #868e96 0%, #adb5bd 100%);
            border-radius: 8px;
        }

        .collapse-toggle.collapsed:hover {
            background: linear-gradient(135deg, #6c757d 0%, #868e96 100%);
        }

        .collapse-content {
            background: white;
            border: 1px solid #e9ecef;
            border-top: none;
            border-radius: 0 0 8px 8px;
        }

        .collapse-item {
            padding: 1.5rem;
            border-bottom: 1px solid #f1f3f4;
        }

        .collapse-item:last-child {
            border-bottom: none;
            border-radius: 0 0 8px 8px;
        }

        .list-group {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
            border: 1px solid #e9ecef;
        }

        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            width: 100%;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #6c5ce7;
            outline: none;
            box-shadow: 0 0 0 0.2rem rgba(108, 92, 231, 0.15);
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        .form-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: block;
            color: #495057;
            font-size: 0.9rem;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            margin: -0.75rem;
        }

        .col-md-6 {
            flex: 0 0 50%;
            padding: 0.75rem;
        }

        .col-md-12 {
            flex: 0 0 100%;
            padding: 0.75rem;
        }

        @media (max-width: 768px) {
            .col-md-6 {
                flex: 0 0 100%;
            }

            .modal-container {
                width: 98%;
                margin: 1%;
            }
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn-secondary:hover {
            background-color: #545b62;
            transform: translateY(-2px);
        }

        .btn-outline-primary {
            border: 2px solid #667eea;
            color: #667eea;
            background: transparent;
        }

        .btn-outline-primary:hover {
            background-color: #667eea;
            color: white;
            transform: translateY(-2px);
        }

        .badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            color: white;
            margin: 0.25rem;
            display: inline-block;
        }

        .bg-info {
            background-color: #17a2b8;
        }

        .bg-warning {
            background-color: #ffc107;
            color: #212529;
        }

        .bg-success {
            background-color: #28a745;
        }

        .form-check {
            display: inline-flex;
            align-items: center;
            margin-left: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .form-check-input {
            margin-left: 0.5rem;
            transform: scale(1.2);
        }

        .form-check-label {
            font-weight: 500;
            cursor: pointer;
        }

        .chevron-icon {
            transition: transform 0.3s ease;
            font-size: 1.1rem;
        }

        .chevron-collapsed {
            transform: rotate(-90deg);
        }

        .modal-footer {
            padding: 1.5rem;
            border-top: 1px solid #dee2e6;
            background-color: #f8f9fa;
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            border-radius: 0 0 12px 12px;
        }

        .invalid-feedback {
            color: #dc3545;
            font-size: 0.8rem;
            margin-top: 0.25rem;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        .notes-container {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            color: white;
            margin: 0.25rem;
            display: inline-block;
            position: relative;
            transition: all 0.2s ease;
        }

        .badge:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .badge i {
            opacity: 0.7;
            transition: all 0.2s ease;
        }

        .badge:hover i {
            opacity: 1;
            color: white;
        }
    </style>

    <!-- Modal -->
    @if($showModal)
        <div class="modal-overlay" wire:click="closeModal"></div>
        <div class="modal-container">
            <!-- Modal Header -->
            <div class="modal-header">
                <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                    <h5 style="margin: 0; font-weight: bold; display: flex; align-items: center; gap: 0.75rem; font-size: 1.3rem;">
                        <i class="fas fa-cogs"></i>
                        تغییرات جزئیات {{ $resident ? $resident->full_name : '' }}
                    </h5>
                    <button wire:click="closeModal"
                            style="background: none; border: none; color: white; font-size: 1.5rem; cursor: pointer; padding: 0.5rem;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">

                <!-- Notes Section -->
                <div class="list-group">
                    <button wire:click="toggleSection('notesInfo')"
                            class="collapse-toggle {{ !$expandedSections['notesInfo'] ? 'collapsed' : '' }}">
                        <strong>یادداشت‌ها و توضیحات</strong>
                        <i class="fas fa-chevron-down chevron-icon {{ !$expandedSections['notesInfo'] ? 'chevron-collapsed' : '' }}"></i>
                    </button>

                    @if($expandedSections['notesInfo'])
                        <div class="collapse-content">
                            <div class="collapse-item">


                                <select class="form-control mt-2 @error('selectedNoteType') is-invalid @enderror"
                                        wire:model.live="selectedNoteType"
                                        wire:change="onNoteTypeChanged">
                                    <option value="">تایپ توضیح رو مشخص کنید</option>
                                    @foreach($this->noteTypes as $key => $noteType)
                                        <option value="{{ $key }}">{{ $noteType }}</option>
                                    @endforeach
                                </select>
                                @error('selectedNoteType')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <div class="row mt-3">
                                    @if($selectedNoteType === 'end_date')
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <label class="form-label">انتخاب تاریخ سررسید:</label>
                                            <input type="text" 
                                                   id="expiry_date_picker" 
                                                   class="form-control" 
                                                   placeholder="روی این فیلد کلیک کنید تا تاریخ را انتخاب کنید"
                                                   style="cursor: pointer; background-color: #fff;">
                                            <small class="text-muted">تاریخ انتخاب شده به صورت خودکار در فیلد یادداشت قرار می‌گیرد</small>
                                        </div>
                                    @endif
                                    
                                    <div class="{{ $selectedNoteType === 'end_date' ? 'col-md-6' : 'col-md-12' }}">
                                        <label class="form-label">یادداشت جدید:</label>
                                        <input wire:model="newNote"
                                               class="form-control @error('selectedNoteType') is-invalid @enderror" 
                                               placeholder="یادداشت خود را اینجا بنویسید..."></input>
                                        @error('newNote')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <button type="button" wire:click="addNote"
                                        class="btn btn-outline-primary mt-3 ">
                                    <i class="fas fa-plus"></i>
                                    افزودن یادداشت
                                </button>

                            </div>

                            <div class="collapse-item">
                                <label class="form-label">یادداشت‌های قبلی:</label>
                                <div class="notes-container">
                                    @foreach($previousNotes as $noteId => $index)
                                        @php
                                            $noteRepository = app(\App\Repositories\NoteRepository::class);
                                            $noteText = $index['note'];
                                            // اگر نوع end_date است، فقط ماه و روز را نمایش بده
                                            if ($index['type'] === 'end_date' && preg_match('/(\d{4})\/(\d{1,2})\/(\d{1,2})/', $noteText, $matches)) {
                                                $noteText = $matches[2] . '/' . $matches[3];
                                            }
                                            $badgeStyle = $noteRepository->getNoteBadgeStyle($index['type']);
                                        @endphp
                                        <span class="badge rounded-pill"
                                              style="{{ $badgeStyle }} position: relative; padding: 8px 25px 8px 12px; margin: 4px; display: inline-block;">
                                            {{ $noteText }}
                                            <i class="fas fa-times-circle"
                                               style="position: absolute; right: 5px; top: 50%; transform: translateY(-50%); cursor: pointer; font-size: 0.75rem; opacity: 0.9;"
                                               wire:click="removeNote({{ $noteId }})"
                                               title="حذف یادداشت"
                                               onmouseover="this.style.opacity='1'; this.style.color='#dc3545';"
                                               onmouseout="this.style.opacity='0.9';"></i>
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Changer  -->
                <div class="list-group">
                    <button wire:click="toggleSection('moving')"
                            class="collapse-toggle {{ !$expandedSections['moving'] ? 'collapsed' : '' }}">
                        <strong>جابجایی</strong>
                        <i class="fas fa-chevron-down chevron-icon {{ !$expandedSections['moving'] ? 'chevron-collapsed' : '' }}"></i>
                    </button>

                    @if($expandedSections['moving'])
                        <div class="collapse-content">
                            <div class="collapse-item">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="">کدام اتاق میخوایید ببرید</label>
                                        <select wire:model="selectBed" class="form-control">
                                            <option value="">انتخاب تخت...</option>
                                            @foreach($this->beds as $bed)
                                                <option value="{{ $bed->id }}" class="{{ $bed->contracts->isEmpty() ? 'bg-warning' : '' }} form-control">
                                                    @if($bed->room && $bed->room->unit)
                                                         {{ $bed->room->unit->name }}،
                                                    @endif
                                                    @if($bed->room)
                                                        اتاق {{ $bed->room->name }}،
                                                    @endif
                                                    تخت {{ $bed->name }}
                                                    @if($bed->contracts->isNotEmpty() && $bed->contracts->first()->resident)
                                                        🛏️ 👤 {{ $bed->contracts->first()->resident->full_name }}
                                                    @else
                                                        ✅ (خالی)
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <button class="btn btn-primary"  wire:click="changeBedForResident({{$resident->id}})"
                                                style="margin-top: 35px; padding-right: 6%;">برو</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- end and delete contract -->
                <div class="list-group">
                    <button wire:click="toggleSection('EndedOrDelete')"
                            class="collapse-toggle {{ !$expandedSections['EndedOrDelete'] ? 'collapsed' : '' }}">
                        <strong>اتمام قرارداد یا حذف کاربر</strong>
                        <i class="fas fa-chevron-down chevron-icon {{ !$expandedSections['EndedOrDelete'] ? 'chevron-collapsed' : '' }}"></i>
                    </button>

                    @if($expandedSections['EndedOrDelete'])
                        <div class="collapse-content">
                            <div class="collapse-item">
                                <div class="row">
                                    <div class="col-md-12">
                                        <button wire:click="endedContract({{ $this->resident->id }})"
                                                class="btn btn-outline-primary">اتمام قرارداد</button>
                                    </div>
                                    <div class="col-md-12">
                                        <button  wire:click="deleteResident({{ $this->resident->id }})"
                                            class="btn btn-danger">حذف اقامتگر</button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    @endif
                </div>



            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button wire:click="closeModal" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    انصراف
                </button>
            </div>
        </div>
    @endif

    @script
    <script>
        (function() {
            // Define function in global scope
            window.initExpiryDatePicker = function() {
                const input = document.getElementById('expiry_date_picker');
                if (!input) {
                    return;
                }

                // Check if jalaliDatepicker is available
                if (typeof jalaliDatepicker === 'undefined') {
                    console.warn('Jalali Datepicker not loaded');
                    return;
                }

                // Check if note type is end_date
                try {
                    const noteType = $wire.get('selectedNoteType');
                    if (noteType !== 'end_date') {
                        return;
                    }
                } catch(e) {
                    console.error('Error getting note type:', e);
                    return;
                }

                // Remove existing event listeners by cloning the element
                const newInput = input.cloneNode(true);
                input.parentNode.replaceChild(newInput, input);

                // Initialize Jalali Datepicker
                jalaliDatepicker.startWatch({
                    date: true,
                    time: false,
                    autoShow: true,
                    autoHide: true,
                    hideAfterChange: true,
                    persianDigits: true,
                    separatorChars: {
                        date: '/',
                        between: ' ',
                        time: ':'
                    },
                    selector: '#expiry_date_picker'
                });

                // Event listener for date change
                newInput.addEventListener('change', function() {
                    const selectedDate = this.value;
                    if (selectedDate) {
                        $wire.set('newNote', selectedDate);
                    }
                });

                // Event listener for input event
                newInput.addEventListener('input', function() {
                    const selectedDate = this.value;
                    if (selectedDate) {
                        $wire.set('newNote', selectedDate);
                    }
                });
            };

            // Watch for selectedNoteType changes via event
            $wire.on('note-type-changed', () => {
                setTimeout(() => {
                    try {
                        const noteType = $wire.get('selectedNoteType');
                        if (noteType === 'end_date') {
                            window.initExpiryDatePicker();
                        }
                    } catch(e) {
                        console.error('Error in note-type-changed:', e);
                    }
                }, 200);
            });

            // Watch for Livewire updates
            Livewire.hook('morph.updated', () => {
                setTimeout(() => {
                    try {
                        const noteType = $wire.get('selectedNoteType');
                        if (noteType === 'end_date') {
                            window.initExpiryDatePicker();
                        }
                    } catch(e) {
                        console.error('Error in morph.updated:', e);
                    }
                }, 200);
            });

            // Initialize when component is ready
            document.addEventListener('livewire:initialized', () => {
                setTimeout(() => {
                    try {
                        const noteType = $wire.get('selectedNoteType');
                        if (noteType === 'end_date') {
                            window.initExpiryDatePicker();
                        }
                    } catch(e) {
                        console.error('Error in livewire:initialized:', e);
                    }
                }, 300);
            });

            // Also initialize on DOMContentLoaded
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => {
                    setTimeout(() => {
                        try {
                            if ($wire && $wire.get('selectedNoteType') === 'end_date') {
                                window.initExpiryDatePicker();
                            }
                        } catch(e) {
                            console.error('Error in DOMContentLoaded:', e);
                        }
                    }, 500);
                });
            } else {
                setTimeout(() => {
                    try {
                        if ($wire && $wire.get('selectedNoteType') === 'end_date') {
                            window.initExpiryDatePicker();
                        }
                    } catch(e) {
                        console.error('Error in immediate init:', e);
                    }
                }, 500);
            }
        })();
    </script>
    @endscript

</div>
