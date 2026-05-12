<fieldset class="fieldset">
    <legend class="legend">
        {{ __('care-plan.condition_diagnosis') ?? 'Стан/діагноз' }}
    </legend>

    <div class="mt-4 index-table-wrapper">
        <table class="index-table">
            <thead class="index-table-thead">
            <tr>
                <th class="index-table-th">
                    {{ __('care-plan.date') }}
                </th>
                <th class="index-table-th">
                    {{ __('care-plan.name') }}
                </th>
            </tr>
            </thead>
            <tbody>

            @forelse($diagnoses as $item)
                <tr class="index-table-tr">
                    <td class="index-table-td">{{ $item['date'] }}</td>
                    <td class="index-table-td-primary">{{ $item['name'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="index-table-td text-center text-gray-400">
                        {{ __('care-plan.no_diagnoses') }}
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @error('form.encounter')
    <p class="text-error mt-2">{{ $message }}</p>
    @enderror
</fieldset>
