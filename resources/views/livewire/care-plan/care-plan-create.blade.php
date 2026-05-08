<x-layouts.patient :personId="$personId" :patientFullName="$patientFullName">
    <div class="breadcrumb-form p-4 shift-content">
        <div x-data="{ activeSection: 'doctors' }"
             class="flex flex-col lg:flex-row gap-8 lg:gap-12"
             @scroll.window.throttle.50ms="
                const sections = ['doctors', 'patient_data', 'care_plan_data', 'condition_diagnosis', 'supporting_information', 'additional_info'];
                for (const section of sections) {
                    const el = document.getElementById(section);
                    if (el) {
                        const rect = el.getBoundingClientRect();
                        if (rect.top <= 150 && rect.bottom >= 150) {
                            activeSection = section;
                            break;
                        }
                    }
                }
             "
        >
            <!-- Main Content -->
            <div class="flex-1 space-y-6 pb-24">
                <div id="doctors" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm scroll-mt-6 overflow-hidden">
                    <div class="record-inner-header border-b border-gray-100 dark:border-gray-700 p-4">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200 flex items-center gap-2">
                            @icon('doctor', 'w-5 h-5 text-primary')
                            {{ __('care-plan.doctors') ?? 'Лікарі' }}
                        </h3>
                    </div>
                    <div class="p-6">
                        @include('livewire.care-plan.parts.doctors')
                    </div>
                </div>

                <div id="patient_data" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm scroll-mt-6 overflow-hidden">
                    <div class="record-inner-header border-b border-gray-100 dark:border-gray-700 p-4">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200 flex items-center gap-2">
                            @icon('patients', 'w-5 h-5 text-primary')
                            {{ __('patients.patient_data') }}
                        </h3>
                    </div>
                    <div class="p-6">
                        @include('livewire.care-plan.parts.patient_data')
                    </div>
                </div>

                <div id="care_plan_data" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm scroll-mt-6 overflow-hidden">
                    <div class="record-inner-header border-b border-gray-100 dark:border-gray-700 p-4">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200 flex items-center gap-2">
                            @icon('hugeicons-contracts', 'w-5 h-5 text-primary')
                            {{ __('care-plan.care_plan_data') ?? 'Дані плану лікування' }}
                        </h3>
                    </div>
                    <div class="p-6">
                        @include('livewire.care-plan.parts.care_plan_data')
                    </div>
                </div>

                <div id="condition_diagnosis" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm scroll-mt-6 overflow-hidden">
                    <div class="record-inner-header border-b border-gray-100 dark:border-gray-700 p-4">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200 flex items-center gap-2">
                            @icon('alert-circle', 'w-5 h-5 text-primary')
                            {{ __('care-plan.condition_diagnosis') ?? 'Стан/Діагноз' }}
                        </h3>
                    </div>
                    <div class="p-6">
                        @include('livewire.care-plan.parts.condition_diagnosis')
                    </div>
                </div>

                <div id="supporting_information" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm scroll-mt-6 overflow-hidden">
                    <div class="record-inner-header border-b border-gray-100 dark:border-gray-700 p-4">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200 flex items-center gap-2">
                            @icon('file-text', 'w-5 h-5 text-primary')
                            {{ __('care-plan.supporting_information') ?? 'Допоміжна інформація' }}
                        </h3>
                    </div>
                    <div class="p-6">
                        @include('livewire.care-plan.parts.supporting_information')
                    </div>
                </div>

                <div class="mt-8 flex items-center gap-4 pt-8 border-t border-gray-100 dark:border-gray-700">
                    <button type="button"
                            class="button-primary-outline flex items-center gap-2 px-6 py-2.5"
                            wire:click="save"
                    >
                        @icon('archive', 'w-4 h-4')
                        {{ __('forms.save') }}
                    </button>

                    <button type="button" @click="$wire.set('showSignatureModal', true)" class="button-primary px-8 py-2.5">
                        {{ __('forms.save_and_send') }}
                    </button>

                    <div class="flex-1"></div>

                    <button type="button" class="button-primary-outline-red px-6 py-2.5">
                        {{ __('forms.delete') }}
                    </button>
                </div>
            </div>

            <!-- Right Sidebar Navigation -->
            <div class="w-full lg:w-[280px] flex-shrink-0 space-y-1 mt-4 lg:mt-0 sticky top-24 self-start">
                @php
                    $navItems = [
                        ['id' => 'doctors', 'label' => __('care-plan.doctors') ?? 'Лікарі', 'icon' => 'doctor'],
                        ['id' => 'patient_data', 'label' => __('patients.patient_data'), 'icon' => 'patients'],
                        ['id' => 'care_plan_data', 'label' => __('care-plan.care_plan_data') ?? 'Дані плану лікування', 'icon' => 'hugeicons-contracts'],
                        ['id' => 'condition_diagnosis', 'label' => __('care-plan.condition_diagnosis') ?? 'Стан/Діагноз', 'icon' => 'alert-circle'],
                        ['id' => 'supporting_information', 'label' => __('care-plan.supporting_information') ?? 'Допоміжна інформація', 'icon' => 'file-text'],
                    ];
                @endphp

                @foreach($navItems as $item)
                    <button @click="
                                activeSection = '{{ $item['id'] }}';
                                document.getElementById('{{ $item['id'] }}').scrollIntoView({ behavior: 'smooth', block: 'start' });
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
        </div>
    </div>

    <x-signature-modal method="sign" />
    <x-forms.loading />
</x-layouts.patient>
