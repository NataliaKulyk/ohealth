<x-layouts.patient :personId="$personId" :patientFullName="$patientFullName">
    <div class="breadcrumb-form p-4 shift-content max-w-5xl mx-auto">

        <div class="space-y-8 pb-24">
            @include('livewire.care-plan.parts.doctors')
            @include('livewire.care-plan.parts.patient_data')
            @include('livewire.care-plan.parts.care_plan_data')
            @include('livewire.care-plan.parts.condition_diagnosis')
            @include('livewire.care-plan.parts.supporting_information')
            @include('livewire.care-plan.parts.additional_info')
            
            <div class="mt-8 flex items-center gap-4 pt-4">
                <button type="button" class="button-primary-outline-red px-6 py-2.5">
                    {{ __('forms.delete') ?? 'Видалити' }}
                </button>

                <button type="button"
                        class="button-primary-outline flex items-center gap-2 px-6 py-2.5"
                        wire:click="save"
                >
                    @icon('archive', 'w-4 h-4')
                    {{ __('forms.save') ?? 'Зберегти' }}
                </button>

                <button type="button" @click="$wire.set('showSignatureModal', true)" class="button-primary px-8 py-2.5">
                    {{ __('care-plan.create_care_plan') ?? 'Створити план лікування' }}
                </button>
            </div>
        </div>
    </div>

    <x-signature-modal method="sign" />
    <x-forms.loading />
</x-layouts.patient>
