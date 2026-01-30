@props(['id', 'title'])

<!-- Modal Backdrop -->
<div x-data="{ show: false }" 
     x-show="show"
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 overflow-y-auto bg-gray-500 bg-opacity-75"
     @show-{{ $id }}.window="show = true"
     @close-{{ $id }}.window="show = false">
    
    <div class="flex min-h-full items-center justify-center p-4 text-center">
        <div x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
            
            <!-- Modal Header -->
            <div class="border-b border-gray-200 dark:border-gray-700 px-4 py-3 sm:px-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">
                    {{ $title }}
                </h3>
            </div>
            
            <!-- Modal Body -->
            <div class="bg-white dark:bg-gray-800 px-4 py-4 sm:p-6 sm:pb-4">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
