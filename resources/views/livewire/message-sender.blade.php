<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">ارسال پیام</h1>
        <p class="mt-2 text-gray-600">مدیریت پیام‌های خوشامدگویی و ارسال پیام به اقامتگران</p>
    </div>

    @if(session()->has('message'))
        <div style="background: #d4edda; color: #155724; padding: 10px; margin: 10px 0; border-radius: 5px;">
            {{ session('message') }}
        </div>
    @endif

    @if(session()->has('error'))
        <div style="background: #f8d7da; color: #721c24; padding: 10px; margin: 10px 0; border-radius: 5px;">
            {{ session('error') }}
        </div>
    @endif

    @if(session()->has('sms_response'))
        @php
            $response = session('sms_response');
        @endphp
        <div style="background: {{ $response['status'] ? '#d4edda' : '#f8d7da' }}; color: {{ $response['status'] ? '#155724' : '#721c24' }}; padding: 15px; margin: 10px 0; border-radius: 5px; border: 1px solid {{ $response['status'] ? '#c3e6cb' : '#f5c6cb' }};">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <h4 style="margin: 0; font-size: 16px;">
                    {{ $response['status'] ? '✅ پیام با موفقیت ارسال شد' : '❌ خطا در ارسال پیام' }}
                </h4>
                <button onclick="this.parentElement.parentElement.remove()" style="background: none; border: none; font-size: 18px; cursor: pointer; color: inherit;">×</button>
            </div>
            <div style="font-size: 14px; line-height: 1.5;">
                <div><strong>نام اقامتگر:</strong> {{ $response['resident_name'] }}</div>
                <div><strong>شماره تماس:</strong> {{ $response['resident_phone'] }}</div>
                <div><strong>پاسخ پیامک:</strong> <code style="background: rgba(0,0,0,0.1); padding: 2px 6px; border-radius: 3px;">{{ $response['rec_id'] }}</code></div>
                <div><strong>توضیح:</strong> {{ $response['message'] }}</div>
            </div>
        </div>
    @endif

    @if(isset($templateDebugs) && count($templateDebugs) > 0)
        @foreach($templateDebugs as $debug)
            <div style="background: #e3f2fd; color: #1565c0; padding: 15px; margin: 10px 0; border-radius: 5px; border: 1px solid #90caf9;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <h4 style="margin: 0; font-size: 16px;">
                        🔍 دیباگ متغیرهای پیام الگو
                    </h4>
                    <button onclick="this.parentElement.parentElement.remove()" style="background: none; border: none; font-size: 18px; cursor: pointer; color: inherit;">×</button>
                </div>
                <div style="font-size: 14px; line-height: 1.5;">
                    <div><strong>اقامتگر:</strong> {{ $debug['resident_name'] }}</div>
                    <div><strong>کد الگو:</strong> {{ $debug['template_body_id'] }}</div>
                    <div style="margin-top: 10px; font-weight: bold;">متغیرها:</div>
                    @foreach($debug['variables'] as $variable)
                        <div style="background: white; padding: 8px; margin: 5px 0; border-radius: 4px; border-left: 4px solid #2196f3;">
                            <div style="font-weight: bold; color: #1565c0;">{{ $variable['variable_code'] }}</div>
                            <div style="font-size: 12px; color: #666;">
                                فیلد: {{ $variable['field_name'] }} | 
                                توضیح: {{ $variable['description'] }}
                            </div>
                            <div style="font-size: 13px; color: #333; margin-top: 3px;">
                                مقدار: <strong>{{ $variable['value'] }}</strong>
                            </div>
                        </div>
                    @endforeach
                    <div style="margin-top: 10px; padding: 8px; background: rgba(33, 150, 243, 0.1); border-radius: 4px;">
                        <strong>آرایه نهایی برای API:</strong> 
                        <code style="background: rgba(0,0,0,0.1); padding: 2px 6px; border-radius: 3px;">
                            {{ json_encode($debug['text_array']) }}
                        </code>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    @if(isset($smsResponses) && count($smsResponses) > 0)
        @foreach($smsResponses as $response)
            <div style="background: {{ $response['status'] ? '#d4edda' : '#f8d7da' }}; color: {{ $response['status'] ? '#155724' : '#721c24' }}; padding: 15px; margin: 10px 0; border-radius: 5px; border: 1px solid {{ $response['status'] ? '#c3e6cb' : '#f5c6cb' }};">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <h4 style="margin: 0; font-size: 16px;">
                        {{ $response['status'] ? '✅ پیام با موفقیت ارسال شد' : '❌ خطا در ارسال پیام' }}
                    </h4>
                    <button onclick="this.parentElement.parentElement.remove()" style="background: none; border: none; font-size: 18px; cursor: pointer; color: inherit;">×</button>
                </div>
                <div style="font-size: 14px; line-height: 1.5;">
                    <div><strong>نام اقامتگر:</strong> {{ $response['resident_name'] }}</div>
                    <div><strong>شماره تماس:</strong> {{ $response['resident_phone'] }}</div>
                    <div><strong>پاسخ پیامک:</strong> <code style="background: rgba(0,0,0,0.1); padding: 2px 6px; border-radius: 3px;">{{ $response['rec_id'] }}</code></div>
                    <div><strong>توضیح:</strong> {{ $response['message'] }}</div>
                    <div><strong>زمان:</strong> {{ $response['created_at'] }}</div>
                </div>
            </div>
        @endforeach
    @endif

    <!-- Welcome Messages Section -->
    <div style="background: white; padding: 20px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3>پیام‌های خوشامدگویی</h3>
            <div style="display: flex; gap: 10px;">
                <button wire:click="sendWelcomeMessages" style="background: #28a745; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer;">
                    ارسال پیام‌های خوشامدگویی
                </button>
                <button wire:click="testSyncWelcomeMessage" style="background: #ffc107; color: #212529; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer;">
                    تست همزمان (برای دیباگ)
                </button>
                <button wire:click="testData" style="background: #17a2b8; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer;">
                    تست داده‌ها
                </button>
                <button wire:click="resetWelcomeForm" style="background: #6c757d; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer;">
                    فرم جدید
                </button>
            </div>
        </div>

        <!-- Welcome Message Form -->
        <form wire:submit="saveWelcomeMessage" style="margin-bottom: 20px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr auto auto; gap: 15px; align-items: end;">
                <div>
                    <label style="display: block; margin-bottom: 5px;">کد الگوی پیام</label>
                    <input type="text" wire:model="body_id" placeholder="مثال: 123456" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    @error('body_id') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px;">تاریخ ارسال</label>
                    <input type="date" wire:model="send_date" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    @error('send_date') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label style="display: flex; align-items: center; margin-top: 20px;">
                        <input type="checkbox" wire:model="is_active" style="margin-left: 5px;">
                        فعال
                    </label>
                </div>
                <div>
                    <button type="submit" style="background: #007bff; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer;">
                        ذخیره پیام
                    </button>
                </div>
            </div>
        </form>

        <!-- Welcome Messages Table -->
        <div style="overflow-x-auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa;">
                        <th style="padding: 10px; text-align: right; border: 1px solid #ddd;">کد الگو</th>
                        <th style="padding: 10px; text-align: right; border: 1px solid #ddd;">عنوان قالب</th>
                        <th style="padding: 10px; text-align: right; border: 1px solid #ddd;">متن قالب</th>
                        <th style="padding: 10px; text-align: right; border: 1px solid #ddd;">تاریخ ارسال</th>
                        <th style="padding: 10px; text-align: right; border: 1px solid #ddd;">وضعیت</th>
                        <th style="padding: 10px; text-align: right; border: 1px solid #ddd;">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($welcomeMessages as $welcomeMessage)
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd;">
                                <code style="background: #f1f3f4; padding: 2px 6px; border-radius: 3px; font-size: 11px;">{{ $welcomeMessage->body_id }}</code>
                            </td>
                            <td style="padding: 10px; border: 1px solid #ddd;">
                                @if($welcomeMessage->template)
                                    {{ $welcomeMessage->template->title }}
                                @else
                                    <span style="color: #999;">قالب یافت نشد</span>
                                @endif
                            </td>
                            <td style="padding: 10px; border: 1px solid #ddd;">
                                @if($welcomeMessage->template)
                                    <div style="max-width: 200px; word-wrap: break-word;">
                                        {{ Str::limit($welcomeMessage->template->body, 50) }}
                                    </div>
                                @else
                                    <span style="color: #999;">-</span>
                                @endif
                            </td>
                            <td style="padding: 10px; border: 1px solid #ddd;">{{ $welcomeMessage->send_date ? $welcomeMessage->send_date->format('Y/m/d') : '-' }}</td>
                            <td style="padding: 10px; border: 1px solid #ddd;">
                                <span style="background: {{ $welcomeMessage->is_active ? '#28a745' : '#dc3545' }}; color: white; padding: 3px 8px; border-radius: 3px; font-size: 12px;">
                                    {{ $welcomeMessage->is_active ? 'فعال' : 'غیرفعال' }}
                                </span>
                            </td>
                            <td style="padding: 10px; border: 1px solid #ddd;">
                                <button wire:click="editWelcomeMessage({{ $welcomeMessage->id }})" style="margin-left: 5px; background: #007bff; color: white; padding: 3px 8px; border: none; border-radius: 3px; cursor: pointer; font-size: 11px;">
                                    ویرایش
                                </button>
                                <button wire:click="toggleActive({{ $welcomeMessage->id }})" style="margin-left: 5px; background: {{ $welcomeMessage->is_active ? '#ffc107' : '#28a745' }}; color: white; padding: 3px 8px; border: none; border-radius: 3px; cursor: pointer; font-size: 11px;">
                                    {{ $welcomeMessage->is_active ? 'غیرفعال' : 'فعال' }}
                                </button>
                                <button wire:click="deleteWelcomeMessage({{ $welcomeMessage->id }})" style="margin-left: 5px; background: #dc3545; color: white; padding: 3px 8px; border: none; border-radius: 3px; cursor: pointer; font-size: 11px;">
                                    حذف
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 20px; text-align: center; color: #666;">
                                هیچ پیام خوشامدگویی یافت نشد.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($welcomeMessages->hasPages())
            <div style="margin-top: 20px;">
                {{ $welcomeMessages->links() }}
            </div>
        @endif
    </div>

    @if(session()->has('send_results'))
        @php
            $results = session('send_results');
        @endphp
        <div style="background: white; padding: 20px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 20px;">
            <h3 style="margin-bottom: 15px;">نتایج ارسال پیام‌ها</h3>
            
            @foreach($results as $result)
                <div style="background: {{ $result['status'] ? '#d4edda' : '#f8d7da' }}; color: {{ $result['status'] ? '#155724' : '#721c24' }}; padding: 15px; margin: 10px 0; border-radius: 5px; border: 1px solid {{ $result['status'] ? '#c3e6cb' : '#f5c6cb' }};">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <h4 style="margin: 0; font-size: 16px;">
                            {{ $result['status'] ? '✅ پیام با موفقیت ارسال شد' : '❌ خطا در ارسال پیام' }}
                        </h4>
                        <button onclick="this.parentElement.parentElement.remove()" style="background: none; border: none; font-size: 18px; cursor: pointer; color: inherit;">×</button>
                    </div>
                    <div style="font-size: 14px; line-height: 1.5;">
                        <div><strong>نام اقامتگر:</strong> {{ $result['resident_name'] }}</div>
                        <div><strong>شماره تماس:</strong> {{ $result['resident_phone'] }}</div>
                        @if($result['status'])
                            <div><strong>کد پیگیری:</strong> <code style="background: rgba(0,0,0,0.1); padding: 2px 6px; border-radius: 3px;">{{ $result['rec_id'] }}</code></div>
                        @else
                            <div><strong>کد خطا:</strong> <code style="background: rgba(0,0,0,0.1); padding: 2px 6px; border-radius: 3px;">{{ $result['rec_id'] }}</code></div>
                        @endif
                        <div><strong>توضیح:</strong> {{ $result['message'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Preview Section -->
    @if($showPreview)
        <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 1000;">
            <div style="background: white; padding: 20px; border-radius: 8px; max-width: 90%; max-height: 90vh; overflow-y: auto;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 style="margin: 0;">پیش‌نمایش اقامتگران واجد شرایط</h3>
                    <button wire:click="closePreview" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #666;">×</button>
                </div>
                
                <div style="margin-bottom: 20px; padding: 15px; background: #e3f2fd; border-radius: 5px;">
                    <h4 style="margin: 0 0 10px 0; color: #1565c0;">تعداد کل: {{ $totalResidentsCount }} اقامتگر</h4>
                    <p style="margin: 0 0 10px 0; color: #666; font-size: 14px;">
                        این اقامتگران شرایط زیر را دارند:
                        <br>• قرارداد فعال (state = active)
                        <br>• پیام خوشامدگویی دریافت نکرده‌اند (welcome_sent = 0)
                        <br>• تاریخ ایجاد اقامتگر بزرگتر از تاریخ ارسال پیام است
                    </p>
                    @if(isset($previewResidents[0]['welcome_message_code']))
                        <div style="padding: 8px; background: #fff; border-radius: 3px; border-left: 4px solid #2196f3;">
                            <strong style="color: #1565c0;">کد پیام (bodyId):</strong>
                            <code style="background: #f1f3f4; padding: 2px 6px; border-radius: 3px; font-size: 12px; margin-right: 5px;">{{ $previewResidents[0]['welcome_message_code'] }}</code>
                        </div>
                    @endif
                </div>

                <div style="overflow-x-auto; margin-bottom: 20px;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                        <thead>
                            <tr style="background: #f8f9fa;">
                                <th style="padding: 10px; text-align: right; border: 1px solid #ddd;">نام اقامتگر</th>
                                <th style="padding: 10px; text-align: right; border: 1px solid #ddd;">شماره تماس</th>
                                <th style="padding: 10px; text-align: right; border: 1px solid #ddd;">اتاق/تخت</th>
                                <th style="padding: 10px; text-align: right; border: 1px solid #ddd;">کد پیام</th>
                                <th style="padding: 10px; text-align: right; border: 1px solid #ddd;">متغیرها</th>
                                <th style="padding: 10px; text-align: right; border: 1px solid #ddd;">پیش‌نمایش پیام</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($previewResidents as $resident)
                                <tr>
                                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $resident['resident_name'] }}</td>
                                    <td style="padding: 8px; border: 1px solid #ddd; direction: ltr;">{{ $resident['resident_phone'] }}</td>
                                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $resident['room_name'] }} / {{ $resident['bed_name'] }}</td>
                                    <td style="padding: 8px; border: 1px solid #ddd;">
                                        <code style="background: #f1f3f4; padding: 2px 6px; border-radius: 3px; font-size: 11px;">{{ $resident['welcome_message_code'] }}</code>
                                    </td>
                                    <td style="padding: 8px; border: 1px solid #ddd;">
                                        @if(isset($resident['variables']) && count($resident['variables']) > 0)
                                            @foreach($resident['variables'] as $variable)
                                                <div style="margin-bottom: 3px; font-size: 12px;">
                                                    <span style="background: #e3f2fd; padding: 2px 4px; border-radius: 2px;">{{ $variable['code'] }}</span>
                                                    <span style="color: #666;">=</span>
                                                    <strong>{{ $variable['value'] }}</strong>
                                                </div>
                                            @endforeach
                                        @else
                                            <span style="color: #999;">متغیری یافت نشد</span>
                                        @endif
                                    </td>
                                    <td style="padding: 8px; border: 1px solid #ddd;">
                                        <div style="max-width: 300px; word-wrap: break-word; font-size: 12px; line-height: 1.4;">
                                            {{ $resident['preview_text'] }}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button wire:click="closePreview" style="padding: 10px 20px; border: 1px solid #ddd; background: #f8f9fa; border-radius: 4px; cursor: pointer;">
                        انصراف
                    </button>
                    <button wire:click="confirmSendMessages" style="padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        تأیید و ارسال پیام‌ها
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Message Modal -->
    @if($showMessageModal)
        <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 1000;">
            <div style="background: white; padding: 20px; border-radius: 8px; max-width: 800px; width: 100%; max-height: 90vh; overflow-y: auto;">
                <h3 style="margin-bottom: 20px;">ارسال پیام جدید</h3>
                
                <form wire:submit="sendMessage">
                    <!-- API Key Selection -->
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: bold;">انتخاب API Key:</label>
                        <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                            @foreach($apiKeys as $apiKey)
                                <button type="button" 
                                        wire:click="selectApiKey({{ $apiKey->id }})"
                                        style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; background: {{ $selectedApiKey == $apiKey->id ? '#007bff' : '#f8f9fa' }}; color: {{ $selectedApiKey == $apiKey->id ? 'white' : 'black' }};">
                                    {{ $apiKey->username }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    @if($selectedApiKey)
                        <!-- Template Selection -->
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">انتخاب قالب از سیستم پیام:</label>
                            <select wire:model="selectedTemplateId" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                                <option value="">انتخاب قالب...</option>
                                @foreach($templates as $template)
                                    <option value="{{ $template->id }}">{{ $template->title }} ({{ $template->body_id }})</option>
                                @endforeach
                            </select>
                            @if($selectedTemplateId)
                                <div style="margin-top: 10px; padding: 10px; background: #e8f5e8; border-radius: 4px;">
                                    <div style="font-size: 12px; color: #6c757d; margin-bottom: 5px;">
                                        <strong>قالب انتخاب شده:</strong> {{ $templates->find($selectedTemplateId)->title ?? '' }}
                                    </div>
                                    <div style="font-family: monospace; font-size: 11px; color: #495057;">
                                        {{ Str::limit($templates->find($selectedTemplateId)->body ?? '', 100) }}
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if($selectedTemplateId)
                            <!-- Variables Selection -->
                            <div style="margin-bottom: 20px;">
                                <label style="display: block; margin-bottom: 5px; font-weight: bold;">انتخاب متغیرها:</label>
                                <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                                    @foreach($variables as $variable)
                                        <button type="button" 
                                                wire:click="toggleVariable({{ $variable->id }})"
                                                style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; background: {{ in_array($variable->id, $selectedVariables) ? '#28a745' : '#f8f9fa' }}; color: {{ in_array($variable->id, $selectedVariables) ? 'white' : 'black' }}">
                                            {{ $variable->code }} - {{ $variable->description }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Message Preview -->
                            <div style="margin-bottom: 20px;">
                                <label style="display: block; margin-bottom: 5px; font-weight: bold;">پیش‌نمایش پیام:</label>
                                <div style="background: #f8f9fa; padding: 15px; border-radius: 4px; border: 1px solid #ddd; min-height: 100px;">
                                    {{ $messageContent ?: 'لطفاً قالب و متغیرها را انتخاب کنید...' }}
                                </div>
                            </div>

                            <!-- Recipient Selection -->
                            <div style="margin-bottom: 20px;">
                                <label style="display: block; margin-bottom: 5px; font-weight: bold;">شماره گیرنده:</label>
                                <input type="text" wire:model="recipientNumber" placeholder="مثال: 09123456789" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                                @error('recipientNumber') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                            </div>

                            <!-- Send Button -->
                            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                                <button type="button" wire:click="closeMessageModal" style="padding: 8px 16px; border: 1px solid #ddd; background: #f8f9fa; border-radius: 4px; cursor: pointer;">
                                    انصراف
                                </button>
                                <button type="submit" style="padding: 8px 16px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
                                    ارسال پیام
                                </button>
                            </div>
                        @endif
                    @endif
                </form>
            </div>
        </div>
    @endif
</div>
