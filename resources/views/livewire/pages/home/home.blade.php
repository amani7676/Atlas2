<div class="wrapper" wire:id="{{ $this->getId() }}">

    <div class="content">
        <div class="container-fluid px-4 mt-4">

            {{-- ردیف سررسید ها و رزرو ها و شبانه ها --}}
            <div class="row">
                {{-- expir date - Static با partial --}}
                <div class="col-lg-7">
                    @include('livewire.pages.home.partials.expirs')
                </div>

                {{-- Reservations & Nightly - Static با partial --}}
                <div class="col-lg-5">
                    @include('livewire.pages.home.partials.reservations')
                    @include('livewire.pages.home.partials.nightly')
                </div>
            </div>

            {{-- ردیف های تخت خالی و خروجی ها --}}
            <div class="row mt-4">
                {{-- Exits - Static با partial --}}
                <div class="col-lg-7">
                    @include('livewire.pages.home.partials.exists')
                </div>

                {{-- Empty Beds - Interactive با Livewire --}}
                <div class="col-lg-5">
                    <livewire:pages.home.componets.empty-beds :allReportService="$allReportService" :beds="$beds"/>
                </div>
            </div>

            {{-- فرم - مدارک - توضیحات --}}
            <div class="row mt-4">
                {{-- Documents - Interactive با Livewire --}}
                <div class="col-12 col-sm-12 col-md-6 col-lg-4 mb-3 mb-md-0">
                    <livewire:pages.home.componets.documetns :allReportService="$allReportService"/>
                </div>

                {{-- Forms - Interactive با Livewire --}}
                <div class="col-12 col-sm-12 col-md-6 col-lg-4 mb-3 mb-md-0">
                    <livewire:pages.home.componets.forms :allReportService="$allReportService"/>
                </div>

                {{-- Debts - Static با partial --}}
                <div class="col-12 col-sm-12 col-md-12 col-lg-4">
                    @include('livewire.pages.home.partials.payments')
                </div>
            </div>

        </div>
    </div>

    @script
    <script>
        // !!! گوش دادن به رویداد 'show-toast'
        window.addEventListener('show-toast', (event) => {
            const params = event.detail[0];

            // !!! فراخوانی cuteToast به جای cuteAlert
            if (typeof window.cuteToast === 'function') {
                cuteToast({
                    type: params.type,
                    title: params.title,
                    description: params.description,
                    timer: params.timer // timer در Toast ضروری است
                });
            } else {
                console.error('cuteToast function is not available on window object.');
            }
        });
        
        // Dispatch delete-note event to Home component only
        let homeComponentId = null;
        
        document.addEventListener('livewire:initialized', () => {
            // Get Home component ID
            const wrapper = document.querySelector('.wrapper[wire\\:id]');
            if (wrapper) {
                homeComponentId = wrapper.getAttribute('wire:id');
            }
        });
        
        window.addEventListener('delete-note-event', (event) => {
            const noteId = event.detail.noteId;
            if (homeComponentId) {
                const homeComponent = Livewire.find(homeComponentId);
                if (homeComponent) {
                    homeComponent.call('handleDeleteNote', noteId);
                }
            } else {
                // Fallback: try to find component
                const wrapper = document.querySelector('.wrapper[wire\\:id]');
                if (wrapper) {
                    const id = wrapper.getAttribute('wire:id');
                    const component = Livewire.find(id);
                    if (component) {
                        component.call('handleDeleteNote', noteId);
                    }
                }
            }
        });
    </script>
    @endscript
</div>
