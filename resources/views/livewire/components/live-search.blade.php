<div class="material-search-wrapper">
    <div class="material-search-container" wire:ignore.self>
        <div class="material-search-input-wrapper">
            <i class="fas fa-search material-search-icon"></i>
            <input
                type="text"
                class="material-search-input"
                placeholder="جستجو بر اساس نام یا تلفن..."
                wire:model.live.debounce.300ms="search"
                wire:focus="$set('showResults', true)"
                wire:blur="$dispatch('hideSearchResults')"
                autocomplete="off"
                id="search-input"
            >
            @if($isLoading)
                <div class="material-search-loading">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
            @endif
            @if($search)
                <button class="material-search-clear" type="button" wire:click="clearSearch" data-ripple>
                    <i class="fas fa-times"></i>
                </button>
            @endif
        </div>

        <!-- نتایج جستجو -->
        @if($showResults)
            <div class="material-search-results">
                @if($searchResults->count() > 0)
                    @foreach($searchResults as $index => $result)
                        <div
                            class="material-search-result-item {{ $selectedIndex === $index ? 'selected' : '' }}"
                            wire:click="selectResult({{ $result->id }})"
                            wire:mouseenter="$set('selectedIndex', {{ $index }})"
                            data-ripple
                        >
                            <div class="material-result-header">
                                <div class="material-result-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="material-result-content">
                                    <div class="material-result-name">
                                        {!! $this->highlightSearch($result->full_name) !!}
                                    </div>
                                    <div class="material-result-phone">
                                        <i class="fas fa-phone"></i>
                                        {!! $this->highlightSearch($result->phone) !!}
                                    </div>
                                </div>
                                @if($result->contract && $result->contract->bed && $result->contract->bed->room)
                                    <a href="{{ route('table_list') }}#{{$result->contract->bed->room->name}}" class="material-result-action" data-ripple>
                                        <i class="fa-solid fa-paper-plane"></i>
                                    </a>
                                @else
                                    <span class="material-result-action" style="opacity: 0.5; cursor: not-allowed;" title="تخت تخصیص یافته ندارد">
                                        <i class="fa-solid fa-paper-plane"></i>
                                    </span>
                                @endif
                            </div>

                            @if($result->contract && $result->contract->bed)
                                <div class="material-result-info">
                                    <span class="material-info-chip material-info-chip-bed">
                                        <i class="fas fa-bed"></i>
                                        تخت: {{ $result->contract->bed->name }}
                                    </span>
                                    @if($result->contract->bed->room)
                                        <span class="material-info-chip material-info-chip-room">
                                            <i class="fas fa-door-open"></i>
                                            اتاق: {{ $result->contract->bed->room->name }}
                                        </span>
                                    @endif
                                    @if($result->contract->bed->room && $result->contract->bed->room->unit)
                                        <span class="material-info-chip material-info-chip-unit">
                                            <i class="fas fa-building"></i>
                                            واحد: {{ $result->contract->bed->room->unit->name }}
                                        </span>
                                        <span class="material-info-chip material-info-chip-date">
                                            <i class="fa-solid fa-calendar-days"></i>
                                            {{ $result->contract->getPaymentDateJalaliAttribute() ?? 'N/A' }}
                                        </span>
                                    @endif
                                </div>
                            @else
                                <div class="material-result-error">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    بدون تخت تخصیص یافته
                                </div>
                            @endif
                        </div>
                    @endforeach
                @elseif(strlen($search) >= 2 && !$isLoading)
                    <div class="material-search-empty">
                        <i class="fas fa-search"></i>
                        <p>نتیجه‌ای یافت نشد</p>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <style>
        /* Material Search Styles */
        .material-search-wrapper {
            position: relative;
        }

        .material-search-container {
            position: relative;
            width: 100%;
            max-width: 400px;
        }

        .material-search-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            background: #f5f5f5;
            border-radius: 24px;
            padding: 8px 16px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid transparent;
        }

        .material-search-input-wrapper:focus-within {
            background: #ffffff;
            border-color: #d4af37;
            box-shadow: 0 2px 8px rgba(212, 175, 55, 0.2);
        }

        .material-search-icon {
            color: #757575;
            margin-left: 8px;
            font-size: 1rem;
        }

        .material-search-input-wrapper:focus-within .material-search-icon {
            color: #d4af37;
        }

        .material-search-input {
            flex: 1;
            border: none;
            background: transparent;
            outline: none;
            padding: 4px 8px;
            font-size: 0.875rem;
            color: #212121;
            direction: rtl;
        }

        .material-search-input::placeholder {
            color: #9e9e9e;
        }

        .material-search-loading {
            margin-left: 8px;
            color: #d4af37;
        }

        .material-search-clear {
            background: transparent;
            border: none;
            color: #757575;
            cursor: pointer;
            padding: 4px;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            margin-left: 4px;
        }

        .material-search-clear:hover {
            background: rgba(0, 0, 0, 0.08);
            color: #212121;
        }

        .material-search-results {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            right: 0;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15), 0 8px 24px rgba(0, 0, 0, 0.1);
            max-height: 400px;
            overflow-y: auto;
            z-index: 1033 !important;
            animation: materialSlideDown 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes materialSlideDown {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .material-search-result-item {
            padding: 16px;
            border-bottom: 1px solid #f5f5f5;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .material-search-result-item:last-child {
            border-bottom: none;
        }

        .material-search-result-item:hover {
            background-color: #fafafa;
        }

        .material-search-result-item.selected {
            background-color: #fff8e1;
            border-right: 3px solid #d4af37;
        }

        .material-result-header {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 12px;
        }

        .material-result-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #d4af37 0%, #b8941f 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .material-result-content {
            flex: 1;
            min-width: 0;
        }

        .material-result-name {
            font-weight: 500;
            font-size: 0.9375rem;
            color: #212121;
            margin-bottom: 4px;
        }

        .material-result-name mark {
            background: #fff9c4;
            color: #d4af37;
            padding: 2px 4px;
            border-radius: 2px;
            font-weight: 600;
        }

        .material-result-phone {
            font-size: 0.8125rem;
            color: #757575;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .material-result-phone i {
            font-size: 0.75rem;
        }

        .material-result-phone mark {
            background: #fff9c4;
            color: #d4af37;
            padding: 1px 3px;
            border-radius: 2px;
            font-weight: 600;
        }

        .material-result-action {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #d4af37;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .material-result-action:hover {
            background: rgba(212, 175, 55, 0.1);
            transform: scale(1.1);
        }

        .material-result-info {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 8px;
        }

        .material-info-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 16px;
            font-size: 0.75rem;
            font-weight: 500;
            white-space: nowrap;
        }

        .material-info-chip i {
            font-size: 0.6875rem;
        }

        .material-info-chip-bed {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .material-info-chip-room {
            background: #e3f2fd;
            color: #1565c0;
        }

        .material-info-chip-unit {
            background: #fff3e0;
            color: #e65100;
        }

        .material-info-chip-date {
            background: #f3e5f5;
            color: #6a1b9a;
        }

        .material-result-error {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #d32f2f;
            font-size: 0.8125rem;
            margin-top: 8px;
        }

        .material-search-empty {
            padding: 32px 16px;
            text-align: center;
            color: #9e9e9e;
        }

        .material-search-empty i {
            font-size: 2rem;
            margin-bottom: 8px;
            color: #bdbdbd;
        }

        .material-search-empty p {
            margin: 0;
            font-size: 0.875rem;
        }
    </style>

    @script
    <script>
        // JavaScript برای بهبود تجربه کاربری
        document.addEventListener('DOMContentLoaded', function() {
            // Hide results when clicking outside
            document.addEventListener('click', function(e) {
                const searchContainer = e.target.closest('.material-search-container');
                if (!searchContainer) {
                    @this.call('hideResults');
                }
            });
        });
    </script>
    @endscript
</div>
