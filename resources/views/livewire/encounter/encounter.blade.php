@php
    $patientName = $patientFullName ?? 'Пацієнт';
    $title = __('patients.encounter') . ' - ' . $patientName;

    $breadcrumbs = [
        ['label' => __('Головна'), 'url' => route('dashboard', [legalEntity()])],
        ['label' => __('Пацієнти'), 'url' => route('persons.index', [legalEntity()])],
        ['label' => $patientName, 'url' => route('persons.summary', [legalEntity(), 'personId' => $personId])],
        ['label' => __('patients.encounter')]
    ];

    $mainGroups = [
        ['id' => 'referral', 'label' => __('patients.referrals'), 'icon' => 'arrow-right', 'view' => 'livewire.encounter.parts.referral'],
        ['id' => 'main-data', 'label' => __('patients.main_data'), 'icon' => 'pie-chart', 'view' => 'livewire.encounter.parts.main-data'],
        ['id' => 'conditions', 'label' => __('patients.diagnoses'), 'icon' => 'file', 'view' => 'livewire.encounter.parts.conditions'],
        ['id' => 'reasons', 'label' => __('patients.reasons_for_visit'), 'icon' => 'person', 'view' => 'livewire.encounter.parts.reasons'],
        ['id' => 'actions', 'label' => __('forms.actions'), 'icon' => 'check-box', 'view' => 'livewire.encounter.parts.actions'],
        ['id' => 'observations', 'label' => __('patients.observation'), 'icon' => 'heart', 'view' => 'livewire.encounter.parts.observations'],
        ['id' => 'immunizations', 'label' => __('patients.immunizations'), 'icon' => 'shield', 'view' => 'livewire.encounter.parts.immunizations'],
        ['id' => 'procedures', 'label' => __('patients.procedures'), 'icon' => 'settings', 'view' => 'livewire.encounter.parts.procedures'],
        ['id' => 'diagnostic-reports', 'label' => __('patients.diagnostic_reports'), 'icon' => 'activity', 'view' => 'livewire.encounter.parts.diagnostic-reports'],
        ['id' => 'clinical-impressions', 'label' => __('patients.clinical_impressions'), 'icon' => 'check', 'view' => 'livewire.encounter.parts.clinical-impressions'],
        ['id' => 'care-plans', 'label' => __('patients.care_plans'), 'icon' => 'clipboard-document-list', 'view' => 'livewire.encounter.parts.care-plan'],
    ];

    $footerItems = [
        ['id' => 'additional-data', 'label' => __('patients.additional_data'), 'icon' => 'details', 'view' => 'livewire.encounter.parts.additional-data'],
    ];
@endphp

<x-layouts.patient :personId="$personId" :patientFullName="$patientFullName">
    <x-slot name="title">{{ $title }}</x-slot>
    <x-slot name="breadcrumbs" :breadcrumbs="$breadcrumbs"></x-slot>

    <div class="breadcrumb-form p-4 shift-content">
        <div x-data="{ activeSection: 'main-data' }" class="flex flex-col lg:flex-row gap-8 lg:gap-12">

            <!-- Main Content -->
            <div class="flex-1 space-y-4">
                @foreach(array_merge($mainGroups, $footerItems) as $item)
                    @if(isset($item['view']))
                        <div id="block-{{ $item['id'] }}"
                             class="bg-white dark:bg-gray-800 rounded-xl scroll-mt-24 shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden"
                             :class="activeSection === '{{ $item['id'] }}' ? 'ring-1 ring-primary/20' : ''"
                        >
                            <button @click="activeSection = activeSection === '{{ $item['id'] }}' ? '' : '{{ $item['id'] }}'"
                                    type="button"
                                    class="w-full flex items-center justify-between p-5 focus:outline-none transition-colors hover:bg-gray-50/50 dark:hover:bg-gray-700/50"
                            >
                                <div class="flex items-center gap-4 text-gray-900 dark:text-gray-100 font-medium text-[15px]">
                                    <span class="w-6 h-6 flex items-center justify-center shrink-0 text-primary">
                                        @icon($item['icon'], 'w-6 h-6')
                                    </span>
                                    <span class="truncate">{{ $item['label'] }}</span>
                                </div>

                                <div class="shrink-0 text-gray-400 dark:text-gray-500 transition-transform duration-300"
                                     :class="activeSection === '{{ $item['id'] }}' ? '' : '-rotate-90'"
                                >
                                    @icon('chevron-down', 'w-5 h-5')
                                </div>
                            </button>

                            <div x-show="activeSection === '{{ $item['id'] }}'"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 -translate-y-2"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-cloak
                                 class="pb-5 px-5"
                                 wire:key="block-content-{{ $item['id'] }}"
                            >
                                @include($item['view'])
                            </div>
                        </div>
                    @endif
                @endforeach

                <!-- Actions -->
                <div class="pt-8 border-t border-gray-100 dark:border-gray-700">
                    <div class="flex flex-wrap gap-4">
                        <button type="button" class="button-primary-outline-red">
                            {{ __('patients.encounter_entered_in_error') ?? 'Взаємодія внесена помилково' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sidebar Navigation (Right) -->
            <div class="w-full lg:w-[300px] flex-shrink-0 space-y-6 mt-4 lg:mt-0 sticky top-24 self-start">
                <div class="space-y-1">
                    @foreach($mainGroups as $item)
                        <button @click="
                                    activeSection = '{{ $item['id'] }}';
                                    document.getElementById('block-{{ $item['id'] }}').scrollIntoView({ behavior: 'smooth', block: 'start' });
                                "
                                type="button"
                                :class="activeSection === '{{ $item['id'] }}' ? 'summary-sidebar-btn-active' : 'summary-sidebar-btn-inactive'"
                                class="summary-sidebar-btn w-full"
                        >
                            <span class="w-5 h-5 flex items-center justify-center shrink-0">
                                @icon($item['icon'], 'w-5 h-5')
                            </span>
                            <span class="truncate">{{ $item['label'] }}</span>
                        </button>
                    @endforeach
                </div>

                <div class="border-t border-gray-100 dark:border-gray-700 my-4"></div>

                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4 border border-gray-100 dark:border-gray-700">
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 px-2">Статус ЕСОЗ</h4>
                    <div class="space-y-3">
                         <div class="px-2 py-1">
                             <div class="text-[11px] text-gray-500 uppercase">Статус підписання</div>
                             <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">Підписано</div>
                         </div>
                         <button type="button" class="button-primary w-full text-sm py-2">Оновити статус</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-signature-modal method="sign" />
    <livewire:components.x-message :key="time()" />
    <x-forms.loading />
</x-layouts.patient>
