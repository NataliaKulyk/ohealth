@props(['id' => null, 'maxWidth' => null, 'placement' => null])

<x-drawer :id="$id" :maxWidth="$maxWidth" :placement="$placement" {{ $attributes }}>
    <div class="h-full flex flex-col justify-between">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <div class="text-lg font-semibold text-gray-900 dark:text-white">
                {{ $title }}
            </div>
            
            <button type="button" 
                    @click="show = false" 
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded-lg p-1.5 inline-flex items-center justify-center focus:outline-none"
            >
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                <span class="sr-only">Close menu</span>
            </button>
        </div>

        <!-- Content -->
        <div class="flex-1 px-6 py-4 overflow-y-auto text-sm text-gray-600 dark:text-gray-400">
            {{ $content }}
        </div>

        <!-- Footer -->
        @isset($footer)
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
                {{ $footer }}
            </div>
        @endisset
    </div>
</x-drawer>
