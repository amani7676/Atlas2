<div style="direction: rtl; text-align: right; padding: 20px;">
    
    @if(session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <h2>مدیریت دسته بندی‌ها</h2>
    
    @if(!$showForm)
        <button type="button" class="btn btn-primary mb-3" wire:click="showForm">
            ایجاد دسته بندی جدید
        </button>
    @else
        <div class="card mb-3">
            <div class="card-body">
                <h5>دسته بندی جدید</h5>
                <div class="mb-3">
                    <label>نام دسته بندی:</label>
                    <input type="text" class="form-control" wire:model="name">
                    @error('name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <button type="button" class="btn btn-success" wire:click="save">ذخیره</button>
                <button type="button" class="btn btn-secondary" wire:click="hideForm">انصراف</button>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <h5>لیست دسته بندی‌ها</h5>
            @if($categories->count() > 0)
                <table class="table">
                    <thead>
                        <tr>
                            <th>نام</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                            <tr>
                                <td>{{ $category->name }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger" wire:click="delete({{ $category->id }})">حذف</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>هیچ دسته بندی یافت نشد.</p>
            @endif
        </div>
    </div>
</div>
