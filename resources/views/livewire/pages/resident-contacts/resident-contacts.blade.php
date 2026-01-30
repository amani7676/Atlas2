<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">
                    <i class="fas fa-phone-alt me-2"></i>
                    شماره تماس اقامتگران
                </h3>
                <div>
                    <button wire:click="loadResidents" class="btn btn-secondary me-2">
                        <i class="fas fa-sync me-2"></i>
                        تازه‌سازی
                    </button>
                    <button wire:click="exportToTxt" class="btn btn-primary">
                        <i class="fas fa-download me-2"></i>
                        خروجی TXT
                    </button>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    @if($residents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ردیف</th>
                                        <th>نام کامل</th>
                                        <th>شماره تماس</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($residents as $index => $resident)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $resident->full_name }}</td>
                                            <td>
                                                @if($this->isValidPhoneNumber($resident->phone))
                                                    <span class="text-success">
                                                        <i class="fas fa-phone me-1"></i>
                                                        {{ $resident->phone }}
                                                    </span>
                                                @else
                                                    <span class="text-danger text-decoration-line-through">
                                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                                        {{ $resident->phone }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        تعداد کل: {{ $residents->count() }} نفر
                                    </small>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">
                                        <i class="fas fa-check-circle text-success me-1"></i>
                                        معتبر: {{ $residents->filter(fn($r) => $this->isValidPhoneNumber($r->phone))->count() }} نفر
                                        <i class="fas fa-exclamation-triangle text-danger me-1 ms-3"></i>
                                        نامعتبر: {{ $residents->filter(fn($r) => !$this->isValidPhoneNumber($r->phone))->count() }} نفر
                                    </small>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-phone-slash fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">هیچ شماره تماسی یافت نشد</h5>
                            <p class="text-muted">اقامتگری با شماره تماس ثبت شده وجود ندارد</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('livewire:init', () => {
    Livewire.on('download-file', (data) => {
        console.log('Data received:', data);
        
        // Livewire sends data as array, get the first element
        const downloadData = Array.isArray(data) ? data[0] : data;
        
        if (!downloadData || !downloadData.content || downloadData.content === 'undefined') {
            alert('خطا: محتوای فایل خالی است');
            return;
        }
        
        alert('دانلود شروع شد: ' + downloadData.filename);
        
        const blob = new Blob([downloadData.content], { type: 'text/plain;charset=utf-8' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = downloadData.filename;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    });
});
</script>
