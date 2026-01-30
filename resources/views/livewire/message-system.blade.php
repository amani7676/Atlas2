<div>
    <h1>سیستم مدیریت پیام</h1>
    
    @if(session()->has('message'))
        <div style="background: #d4edda; color: #155724; padding: 10px; margin: 10px 0; border-radius: 5px;">
            {{ session('message') }}
        </div>
    @endif

    <div style="margin-bottom: 20px;">
        <button wire:click="setActiveTab('api-keys')" style="{{ $activeTab === 'api-keys' ? 'background: #007bff; color: white;' : 'background: #f8f9fa;' }} padding: 10px 20px; margin: 0 5px; border: 1px solid #ddd; cursor: pointer;">
            API Keys
        </button>
        <button wire:click="setActiveTab('variables')" style="{{ $activeTab === 'variables' ? 'background: #007bff; color: white;' : 'background: #f8f9fa;' }} padding: 10px 20px; margin: 0 5px; border: 1px solid #ddd; cursor: pointer;">
            متغیرها
        </button>
        <button wire:click="setActiveTab('templates')" style="{{ $activeTab === 'templates' ? 'background: #007bff; color: white;' : 'background: #f8f9fa;' }} padding: 10px 20px; margin: 0 5px; border: 1px solid #ddd; cursor: pointer;">
            قالب‌ها
        </button>
    </div>

    @if($activeTab === 'api-keys')
        <div style="background: white; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3>API Keys</h3>
                <button wire:click="createApiKey" style="background: #28a745; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer;">
                    افزودن API Key
                </button>
            </div>
            
            @forelse($apiKeys as $apiKey)
                <div style="border: 1px solid #eee; padding: 15px; margin-bottom: 10px; border-radius: 5px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong>{{ $apiKey->username }}</strong>
                            <br>
                            <code>{{ Str::limit($apiKey->api_key, 30) }}...</code>
                        </div>
                        <div>
                            <span style="background: {{ $apiKey->is_active ? '#28a745' : '#dc3545' }}; color: white; padding: 3px 8px; border-radius: 3px; font-size: 12px;">
                                {{ $apiKey->is_active ? 'فعال' : 'غیرفعال' }}
                            </span>
                            <button wire:click="editApiKey({{ $apiKey->id }})" style="margin-left: 10px; background: #007bff; color: white; padding: 5px 10px; border: none; border-radius: 3px; cursor: pointer;">
                                ویرایش
                            </button>
                            <button wire:click="deleteApiKey({{ $apiKey->id }})" style="margin-left: 5px; background: #dc3545; color: white; padding: 5px 10px; border: none; border-radius: 3px; cursor: pointer;">
                                حذف
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <p style="text-align: center; color: #666; padding: 20px;">هیچ API Key یافت نشد.</p>
            @endforelse
        </div>
    @endif

    @if($activeTab === 'variables')
        <div style="background: white; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3>متغیرها</h3>
                <button wire:click="createVariable" style="background: #28a745; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer;">
                    افزودن متغیر
                </button>
            </div>
            
            @forelse($variables as $variable)
                <div style="border: 1px solid #eee; padding: 15px; margin-bottom: 10px; border-radius: 5px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <code>{{ $variable->code }}</code>
                            <br>
                            <strong>{{ $variable->description }}</strong>
                            <br>
                            <small style="color: #666;">{{ $variable->field_name }}</small>
                        </div>
                        <div>
                            <span style="background: {{ $variable->is_active ? '#28a745' : '#dc3545' }}; color: white; padding: 3px 8px; border-radius: 3px; font-size: 12px;">
                                {{ $variable->is_active ? 'فعال' : 'غیرفعال' }}
                            </span>
                            <button wire:click="editVariable({{ $variable->id }})" style="margin-left: 10px; background: #007bff; color: white; padding: 5px 10px; border: none; border-radius: 3px; cursor: pointer;">
                                ویرایش
                            </button>
                            <button wire:click="deleteVariable({{ $variable->id }})" style="margin-left: 5px; background: #dc3545; color: white; padding: 5px 10px; border: none; border-radius: 3px; cursor: pointer;">
                                حذف
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <p style="text-align: center; color: #666; padding: 20px;">هیچ متغیری یافت نشد.</p>
            @endforelse
        </div>
    @endif

    @if($activeTab === 'templates')
        <div style="background: white; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div>
                    <h3>قالب‌های پیام</h3>
                    <p style="color: #666; margin: 5px 0 0 0;">قالب‌های دریافتی از سیستم پیامک</p>
                </div>
                <div style="display: flex; align-items: center;">
                    <select wire:model="selectedApiKey" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px; margin-left: 10px;">
                        <option value="">انتخاب API Key</option>
                        @foreach($apiKeys as $apiKey)
                            <option value="{{ $apiKey->id }}">{{ $apiKey->username }}</option>
                        @endforeach
                    </select>
                    <button wire:click="syncTemplates" style="background: #17a2b8; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; margin-left: 5px;">
                        همگام‌سازی قالب‌ها
                    </button>
                    <button wire:click="showLogs" style="background: #6c757d; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; margin-left: 5px;">
                        نمایش لاگ‌ها
                    </button>
                </div>
            </div>
            
            @if($showLogs)
                <div style="background: #f8f9fa; border: 1px solid #ddd; border-radius: 5px; padding: 15px; margin-bottom: 20px;">
                    <h4 style="margin: 0 0 10px 0;">لاگ‌های اخیر:</h4>
                    <div style="background: #000; color: #00ff00; padding: 10px; border-radius: 3px; font-family: monospace; font-size: 12px; max-height: 300px; overflow-y: auto;">
                        @foreach($logs as $log)
                            <div>{{ $log }}</div>
                        @endforeach
                    </div>
                </div>
            @endif
            
            @forelse($templates as $template)
                <div style="border: 1px solid #eee; padding: 15px; margin-bottom: 10px; border-radius: 5px;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div style="flex: 1;">
                            <strong>{{ $template->title }}</strong>
                            <br>
                            <code style="background: #f1f3f4; padding: 2px 6px; border-radius: 3px; font-size: 11px; color: #6c757d;">{{ $template->body_id }}</code>
                            <br>
                            <p style="color: #666; margin: 5px 0;">{{ Str::limit($template->body, 100) }}</p>
                            <small style="color: #999;">
                                {{ $template->insert_date ? $template->insert_date->format('Y/m/d H:i') : '-' }}
                            </small>
                        </div>
                        <span style="background: {{ $template->is_active ? '#28a745' : '#dc3545' }}; color: white; padding: 3px 8px; border-radius: 3px; font-size: 12px;">
                            {{ $template->is_active ? 'فعال' : 'غیرفعال' }}
                        </span>
                    </div>
                </div>
            @empty
                <p style="text-align: center; color: #666; padding: 20px;">هیچ قالبی یافت نشد.</p>
            @endforelse
        </div>
    @endif

    @if($showApiKeyModal)
        <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 1000;">
            <div style="background: white; padding: 20px; border-radius: 8px; max-width: 500px; width: 100%;">
                <h3>{{ $apiKeyId ? 'ویرایش API Key' : 'افزودن API Key' }}</h3>
                <form wire:submit="saveApiKey">
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px;">نام کاربری</label>
                        <input type="text" wire:model="username" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        @error('username') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px;">API Key</label>
                        <input type="text" wire:model="api_key" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        @error('api_key') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display: flex; align-items: center;">
                            <input type="checkbox" wire:model="is_active" style="margin-left: 5px;">
                            فعال
                        </label>
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 10px;">
                        <button type="button" wire:click="closeApiKeyModal" style="padding: 8px 16px; border: 1px solid #ddd; background: #f8f9fa; border-radius: 4px; cursor: pointer;">
                            انصراف
                        </button>
                        <button type="submit" style="padding: 8px 16px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
                            ذخیره
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if($showVariableModal)
        <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 1000;">
            <div style="background: white; padding: 20px; border-radius: 8px; max-width: 500px; width: 100%;">
                <h3>{{ $variableId ? 'ویرایش متغیر' : 'افزودن متغیر' }}</h3>
                <form wire:submit="saveVariable">
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px;">کد متغیر</label>
                        <input type="text" wire:model="code" placeholder="مثال: {0}, {1}, {2}" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        @error('code') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px;">توضیحات</label>
                        <input type="text" wire:model="description" placeholder="مثال: نام اقامتگر، شماره اتاق" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        @error('description') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px;">نام فیلد (دیتابیس)</label>
                        <select wire:model="field_name" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                            <option value="">انتخاب از لیست</option>
                            <optgroup label="فیلدهای جدول Residents">
                                <option value="id">residents.id</option>
                                <option value="full_name">residents.full_name</option>
                                <option value="phone">residents.phone</option>
                                <option value="age">residents.age</option>
                                <option value="birth_date">residents.birth_date</option>
                                <option value="job">residents.job</option>
                                <option value="referral_source">residents.referral_source</option>
                                <option value="form">residents.form</option>
                                <option value="document">residents.document</option>
                                <option value="rent">residents.rent</option>
                                <option value="trust">residents.trust</option>
                                <option value="created_at">residents.created_at</option>
                                <option value="updated_at">residents.updated_at</option>
                                <option value="deleted_at">residents.deleted_at</option>
                            </optgroup>
                            <optgroup label="فیلدهای جدول Contracts">
                                <option value="resident_id">contracts.resident_id</option>
                                <option value="payment_date">contracts.payment_date</option>
                                <option value="bed_id">contracts.bed_id</option>
                                <option value="state">contracts.state</option>
                                <option value="start_date">contracts.start_date</option>
                                <option value="end_date">contracts.end_date</option>
                                <option value="welcome_sent">contracts.welcome_sent</option>
                                <option value="welcome_message_response">contracts.welcome_message_response</option>
                                <option value="created_at">contracts.created_at</option>
                                <option value="updated_at">contracts.updated_at</option>
                                <option value="deleted_at">contracts.deleted_at</option>
                            </optgroup>
                            <optgroup label="فیلدهای دستی">
                                <option value="room_name">rooms.name</option>
                                <option value="bed_name">beds.name</option>
                            </optgroup>
                            <option value="custom">ورود دستی</option>
                        </select>
                        @error('field_name') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                    </div>
                    <small style="color: #666; font-size: 11px; margin-top: 3px; display: block;">
                        نام فیلد به صورت انگلیسی در دیتابیس ذخیره می‌شود
                    </small>
                </div>
                @if($field_name === 'custom')
                        <div style="margin-bottom: 15px; margin-top: 10px; padding: 10px; background: #f8f9fa; border-radius: 4px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">ورود دستی نام فیلد:</label>
                            <input type="text" wire:model="custom_field_name" placeholder="مثال: custom_field_name" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                            <small style="color: #666; font-size: 11px; display: block; margin-top: 3px;">
                                نام فیلد را به صورت دستی وارد کنید (مثال: custom_field_name)
                            </small>
                        </div>
                    @endif
                    <div style="margin-bottom: 15px;">
                        <label style="display: flex; align-items: center;">
                            <input type="checkbox" wire:model="variable_is_active" style="margin-left: 5px;">
                            فعال
                        </label>
                    </div>
                </form>
                
                <!-- Buttons outside the form -->
                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 15px;">
                    <button wire:click="closeVariableModal" style="padding: 8px 16px; border: 1px solid #ddd; background: #f8f9fa; border-radius: 4px; cursor: pointer;">
                        انصراف
                    </button>
                    <button wire:click="saveVariable" style="padding: 8px 16px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        ذخیره
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
