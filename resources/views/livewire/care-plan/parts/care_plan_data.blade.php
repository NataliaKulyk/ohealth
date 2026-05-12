<fieldset class="fieldset">
    <legend class="legend">
        {{ __('care-plan.care_plan_data') }}
    </legend>

    <div class="form-row-2">
        <div class="form-group group">
            <label for="category" class="label">
                {{ __('care-plan.category') }}
            </label>

            <select id="category"
                    name="category"
                    class="input-select peer"
                    wire:model="form.category"
            >
                <option value="">{{ __('forms.select') }}</option>
                @foreach($categories as $code => $description)
                    <option value="{{ $code }}">{{ $description }}</option>
                @endforeach
            </select>

            @error('form.category')
            <p class="text-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group group">
            <input type="text"
                   name="title"
                   id="title"
                   class="input peer"
                   placeholder=" "
                   autocomplete="off"
                   wire:model="form.title"
                   required
            >
            <label for="title" class="label">
                {{ __('care-plan.name_care_plan') }}
            </label>
            @error('form.title')
            <p class="text-error">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="form-row-2 mt-5">
        <div class="form-group group">
            <label for="intent" class="label">
                {{ __('care-plan.intention') }}
            </label>

            <select id="intent"
                    name="intent"
                    class="input-select peer"
                    wire:model="form.intent"
            >
                <option value="order">{{ __('care-plan.order') ?? 'Призначення' }}</option>
                <option value="proposal">{{ __('care-plan.proposal') ?? 'Пропозиція' }}</option>
                <option value="plan">{{ __('care-plan.plan') ?? 'План' }}</option>
            </select>

            @error('form.intent')
            <p class="text-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group group">
            <label for="context" class="label">
                {{ __('care-plan.conditions_of_service') ?? 'Умови надання послуг' }}
            </label>
            <select id="context"
                    name="context"
                    class="input-select peer"
                    wire:model="form.context"
            >
                <option value="">{{ __('forms.select') }}</option>
                @isset($dictionaries['encounter_classes'])
                    @foreach($dictionaries['encounter_classes'] as $code => $description)
                        <option value="{{ $code }}">{{ $description }}</option>
                    @endforeach
                @endisset
            </select>
            @error('form.context')
            <p class="text-error">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="mt-5 space-y-5">
        <div class="w-full lg:w-1/2">
            <label class="label mb-2 block">
                {{ __('care-plan.date_and_time_start') }}
            </label>
            <div class="flex items-center gap-4">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        @icon('calendar', 'w-5 h-5 text-gray-500')
                    </div>
                    <input type="text"
                           name="period_start"
                           id="period_start"
                           class="peer input pl-10 appearance-none datepicker-input dark:text-white w-full"
                           placeholder=" "
                           required
                           datepicker-autohide
                           datepicker-format="{{ frontendDateFormat() }}"
                           datepicker-button="false"
                           wire:model.lazy="form.period_start"
                    />
                </div>
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        @icon('clock', 'w-5 h-5 text-gray-500')
                    </div>
                    <input type="time"
                           name="period_start_time"
                           id="period_start_time"
                           class="peer input pl-10 appearance-none dark:text-white w-full"
                           wire:model.lazy="form.period_start_time"
                    />
                </div>
            </div>
            <div class="flex justify-between w-full mt-1">
                @error('form.period_start') <p class="text-error text-xs">{{ $message }}</p> @enderror
                @error('form.period_start_time') <p class="text-error text-xs">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="w-full lg:w-1/2">
            <label class="label mb-2 block">
                {{ __('care-plan.date_and_time_end') }}
            </label>
            <div class="flex items-center gap-4">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        @icon('calendar', 'w-5 h-5 text-gray-500')
                    </div>
                    <input type="text"
                           name="period_end"
                           id="period_end"
                           class="peer input pl-10 appearance-none datepicker-input dark:text-white w-full"
                           placeholder=" "
                           datepicker-autohide
                           datepicker-format="{{ frontendDateFormat() }}"
                           datepicker-button="false"
                           wire:model.lazy="form.period_end"
                    />
                </div>
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        @icon('clock', 'w-5 h-5 text-gray-500')
                    </div>
                    <input type="time"
                           name="period_end_time"
                           id="period_end_time"
                           class="peer input pl-10 appearance-none dark:text-white w-full"
                           wire:model.lazy="form.period_end_time"
                    />
                </div>
            </div>
            <div class="flex justify-between w-full mt-1">
                @error('form.period_end') <p class="text-error text-xs">{{ $message }}</p> @enderror
                @error('form.period_end_time') <p class="text-error text-xs">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    {{-- Warning shown always when period_end has a value (per TZ 3.10.1.2.4) --}}
    @if(!empty($form['period_end']))
    <div class="bg-red-100 rounded-lg mt-4">
        <div class="p-4">
            <div class="flex items-center gap-2 mb-2">
                @icon('alert-circle', 'w-5 h-5 text-red-700')
                <p class="font-semibold text-red-700">{{ __('care-plan.attention') }}</p>
            </div>
            <p class="text-sm text-red-700">{{ __('care-plan.you_specify_the_end_date') }}</p>
        </div>
    </div>
    @endif
</fieldset>
