<div>
    <h3>تست Livewire</h3>
    <p>شمارنده: {{ $count }}</p>
    <button type="button" class="btn btn-primary" wire:click="increment">افزودن</button>
    <button type="button" class="btn btn-success" wire:click="showMessage">نمایش پیام</button>
    
    @if(session()->has('success'))
        <div class="alert alert-success mt-3">
            {{ session('success') }}
        </div>
    @endif
</div>
