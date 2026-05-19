<?php

declare(strict_types=1);

namespace App\Livewire\Encounter\Forms;

use App\Core\BaseForm;
use App\Rules\Cyrillic;
use App\Rules\InDictionary;
use App\Rules\OnlyOnePrimaryDiagnosis;
use App\Rules\PastDateTime;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\RequiredIf;

class EncounterForm extends BaseForm
{
    public array $encounter = [
        'diagnoses' => [],
        'reasons' => [],
        'actions' => [],
        'referralType' => ''
    ];

    public array $episode = ['id' => '', 'typeCode' => '', 'name' => ''];

    public array $conditions;

    public array $immunizations;

    public array $observations;

    public array $diagnosticReports;

    public array $procedures;

    public array $clinicalImpressions;

    protected function rules(): array
    {
        $rules = [
            'episode.id' => [
                'nullable',
                Rule::requiredIf($this->component->episodeType === 'existing'),
                'string',
                'uuid'
            ],
            'episode.typeCode' => [
                'nullable',
                Rule::requiredIf($this->component->episodeType === 'new'),
                'string',
                new InDictionary('eHealth/episode_types')
            ],
            'episode.name' => [
                'nullable',
                Rule::requiredIf($this->component->episodeType === 'new'),
                'string',
                'max:255',
                new Cyrillic()
            ],
            'encounter.visit.identifier.value' => ['nullable', 'string', 'max:64'],
            'encounter.periodDate' => ['required', 'date', new PastDateTime()],
            'encounter.periodStart' => ['required'],
            'encounter.periodEnd' => ['required', 'after_or_equal:encounter.periodStart'],
            'encounter.classCode' => ['required', 'string', new InDictionary('eHealth/encounter_classes')],
            'encounter.typeCode' => ['required', 'string', new InDictionary('eHealth/encounter_types')],
            'encounter.priorityCode' => [
                'nullable',
                new RequiredIf($this->encounter['classCode'] !== 'PHC'),
                'string',
                new InDictionary('eHealth/encounter_priority')
            ],
            'encounter.serviceType' => ['nullable', 'string', new InDictionary('eHealth/encounter_service_types')],
            'encounter.reasons' => [
                'nullable',
                new RequiredIf($this->encounter['classCode'] === 'PHC'),
                'array',
                'min:1'
            ],
            'encounter.reasons.*' => ['string', new InDictionary('eHealth/ICPC2/reasons')],
            'encounter.diagnoses' => [
                'nullable',
                Rule::requiredUnless('encounter.classCode', 'PHC'),
                'array',
                'min:1',
                new OnlyOnePrimaryDiagnosis()
            ],
            'encounter.diagnoses.*.conditionCode' => ['required', 'string', new InDictionary([
                'eHealth/ICPC2/condition_codes',
                'eHealth/ICD10_AM/condition_codes'
            ])],
            'encounter.diagnoses.*.roleCode' => ['required', 'string', new InDictionary('eHealth/diagnosis_roles')],
            'encounter.diagnoses.*.rank' => ['nullable', 'integer', 'min:1'],
            'encounter.divisionId' => [
                'nullable',
                new RequiredIf(in_array($this->encounter['classCode'], ['AMB', 'EMER'], true)),
                Rule::prohibitedIf($this->encounter['classCode'] === 'PHC'),
                'string',
                'uuid'
            ],
            'encounter.paperReferral.number' => ['nullable', 'string', 'max:64'],
            'encounter.paperReferral.date' => ['nullable', 'date', new PastDateTime()],
            'encounter.paperReferral.publicName' => ['nullable', 'string', 'max:255'],
            'encounter.paperReferral.serviceType' => ['nullable', 'string', new InDictionary('eHealth/encounter_service_types')],

            'conditions.*.clinicalStatus' => ['required', 'string', new InDictionary('eHealth/condition_clinical_statuses')],
            'conditions.*.verificationStatus' => ['required', 'string', new InDictionary('eHealth/condition_verification_statuses')],
            'conditions.*.severity' => ['nullable', 'string', new InDictionary('eHealth/condition_severities')],
            'conditions.*.codeCode' => ['required', 'string'],
            'conditions.*.codeSystem' => ['required', 'string'],
            'conditions.*.onsetDate' => ['nullable', 'date', new PastDateTime()],
            'conditions.*.primarySource' => ['required', 'boolean'],
            'conditions.*.reportOrigin' => ['nullable', 'string', new InDictionary('eHealth/report_origins')],
            'conditions.*.notes' => ['nullable', 'string', 'max:4000'],
            'conditions.*.evidenceDetails.*.id' => ['required', 'string', 'uuid'],
            'conditions.*.evidenceDetails.*.type' => ['required', 'string', 'in:condition,observation'],

            'immunizations.*.status' => ['required', 'string', new InDictionary('eHealth/immunization_statuses')],
            'immunizations.*.notGiven' => ['required', 'boolean'],
            'immunizations.*.vaccineCode' => ['required', 'string', new InDictionary('eHealth/vaccine_codes')],
            'immunizations.*.occurrenceDate' => ['required', 'date', new PastDateTime()],
            'immunizations.*.primarySource' => ['required', 'boolean'],
            'immunizations.*.reportOrigin' => ['nullable', 'string', new InDictionary('eHealth/immunization_report_origins')],
            'immunizations.*.lotNumber' => ['nullable', 'string', 'max:64'],
            'immunizations.*.expirationDate' => ['nullable', 'date'],
            'immunizations.*.siteCode' => ['nullable', 'string', new InDictionary('eHealth/immunization_body_sites')],
            'immunizations.*.routeCode' => ['nullable', 'string', new InDictionary('eHealth/vaccination_routes')],
            'immunizations.*.doseQuantity' => ['nullable', 'numeric', 'min:0'],
            'immunizations.*.doseUnitCode' => ['nullable', 'string', new InDictionary('eHealth/ucum/units')],
            'immunizations.*.explanationReasonCode' => ['nullable', 'string', new InDictionary('eHealth/reason_explanations')],
            'immunizations.*.explanationReasonNotGivenCode' => ['nullable', 'string', new InDictionary('eHealth/reason_not_given_explanations')],
            'immunizations.*.vaccinationProtocols.*.description' => ['nullable', 'string', 'max:255'],
            'immunizations.*.vaccinationProtocols.*.authorityCode' => ['nullable', 'string', new InDictionary('eHealth/vaccination_authorities')],
            'immunizations.*.vaccinationProtocols.*.targetDiseaseCodes' => ['required', 'array', 'min:1'],
            'immunizations.*.vaccinationProtocols.*.targetDiseaseCodes.*' => ['string', new InDictionary('eHealth/vaccination_target_diseases')],
            'immunizations.*.vaccinationProtocols.*.doseSequence' => ['nullable', 'integer', 'min:1'],

            'diagnosticReports.*.status' => ['required', 'string', 'in:final,preliminary'],
            'diagnosticReports.*.categoryCode' => ['required', 'string', new InDictionary('eHealth/diagnostic_report_categories')],
            'diagnosticReports.*.codeCode' => ['required', 'string', new InDictionary('eHealth/LOINC/observation_codes')],
            'diagnosticReports.*.issuedDate' => ['required', 'date', new PastDateTime()],
            'diagnosticReports.*.conclusion' => ['nullable', 'string', 'max:4000'],
            'diagnosticReports.*.conclusionCode' => ['nullable', 'string'],

            'observations.*.status' => ['required', 'string', new InDictionary('eHealth/observation_statuses')],
            'observations.*.categoryCode' => ['required', 'string', new InDictionary('eHealth/observation_categories')],
            'observations.*.codeCode' => ['required', 'string', new InDictionary([
                'eHealth/LOINC/observation_codes',
                'eHealth/custom/observation_codes'
            ])],
            'observations.*.effectiveDate' => ['required', 'date', new PastDateTime()],
            'observations.*.issuedDate' => ['nullable', 'date', new PastDateTime()],
            'observations.*.valueType' => ['required', 'string', 'in:valueQuantity,valueCodeableConcept,valueString,valueBoolean,valueInteger,valueDateTime,valuePeriod,valueSampledData'],
            'observations.*.valueString' => ['nullable', 'string', 'max:4000'],
            'observations.*.bodySiteCode' => ['nullable', 'string', new InDictionary('eHealth/body_sites')],
            'observations.*.methodCode' => ['nullable', 'string', new InDictionary('eHealth/observation_methods')],

            'procedures.*.status' => ['required', 'string', new InDictionary('eHealth/procedure_statuses')],
            'procedures.*.categoryCode' => ['nullable', 'string', new InDictionary('eHealth/procedure_categories')],
            'procedures.*.codeCode' => ['required', 'string', new InDictionary('custom/services')],
            'procedures.*.performedDate' => ['required', 'date', new PastDateTime()],
            'procedures.*.outcomeCode' => ['nullable', 'string', new InDictionary('eHealth/procedure_outcomes')],
            'procedures.*.notes' => ['nullable', 'string', 'max:4000'],
            'procedures.*.reasonReferences.*.id' => ['required', 'string', 'uuid'],
            'procedures.*.reasonReferences.*.type' => ['required', 'string', 'in:condition,observation'],

            'clinicalImpressions.*.status' => ['required', 'string', 'in:in-progress,completed,entered-in-error'],
            'clinicalImpressions.*.date' => ['required', 'date', new PastDateTime()],
            'clinicalImpressions.*.summary' => ['nullable', 'string', 'max:4000'],
            'clinicalImpressions.*.supportingInfo.*.id' => ['required', 'string', 'uuid'],
            'clinicalImpressions.*.supportingInfo.*.type' => ['required', 'string', 'in:condition,observation,procedure,diagnosticReport,encounter'],
        ];

        if ($this->component->episodeType === 'new') {
            $this->addAllowedEpisodeCareManagerEmployeeTypes($rules);
        }

        $this->addAllowedEncounterClasses($rules);
        $this->addAllowedEncounterTypes($rules);
        $this->addAllowedConditionCodes($rules);
        $this->addPsychiatryEvidenceValidation($rules);
        $this->addEmployeeTypeConditionsValidation($rules);
        $this->addSpecialityConditionsValidation($rules);

        foreach ($this->immunizations ?? [] as $i => $immunization) {
            foreach ($immunization['vaccinationProtocols'] ?? [] as $p => $protocol) {
                $rules["immunizations.$i.vaccinationProtocols.$p.doseSequence"][] = $this->requiredIfProtocolFieldsMandatory("immunizations.$i.vaccinationProtocols.$p.doseSequence");
            }
        }

        foreach ($this->procedures ?? [] as $i => $procedure) {
            foreach ($procedure['reasonReferences'] ?? [] as $r => $reference) {
                $rules["procedures.$i.reasonReferences.$r.code"] = $this->reasonReferenceCodeRule("procedures.$i.reasonReferences.$r.code");
            }
        }

        return $rules;
    }

    /**
     * @return array
     */
    public function signingRules(): array
    {
        return [
            'knedp' => ['required', 'string'],
            'keyContainerUpload' => ['required', 'file'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * @return array
     */
    protected function messages(): array
    {
        return [
            'encounter.priorityCode.required_if' => __('validation.custom.encounter.priorityCode.required_if'),
            'encounter.reasons.required_if' => __('validation.custom.encounter.reasons.required_if'),
            'encounter.diagnoses.required_unless' => __('validation.custom.encounter.diagnoses.required_unless'),
            'encounter.divisionId.required_if' => __('validation.custom.encounter.divisionId.required_if'),
            'encounter.divisionId.prohibited' => __('validation.custom.encounter.divisionId.prohibited'),
            'encounter.actions.required_if' => __('validation.custom.encounter.actions.required_if'),
            'encounter.actions.prohibited_unless' => __('validation.custom.encounter.actions.prohibited_unless'),
        ];
    }

    /**
     * Add allowed values for episode type code.
     *
     * @param  array  $rules
     * @return void
     */
    private function addAllowedEpisodeCareManagerEmployeeTypes(array &$rules): void
    {
        $allowedValues = array_intersect(
            config('ehealth.legal_entity_episode_types')[legalEntity()->type->name],
            config('ehealth.employee_episode_types')[Auth::user()->getEncounterWriterEmployee()->employeeType]
        );
        $rules['episode.typeCode'][] = 'in:' . implode(',', $allowedValues);
    }

    /**
     * Add allowed values for encounter classes.
     *
     * @param  array  $rules
     * @return void
     */
    private function addAllowedEncounterClasses(array &$rules): void
    {
        $rules['encounter.classCode'][] = function (string $attribute, mixed $value, Closure $fail): void {
            $episodeTypeCode = $this->episode['typeCode'] ?? null;

            if (empty($episodeTypeCode) && !empty($this->episode['id'])) {
                $episode = collect($this->component->episodes)
                    ->firstWhere('uuid', $this->episode['id']);
                $episodeTypeCode = data_get($episode, 'type.code');
            }

            if (empty($episodeTypeCode)) {
                return;
            }

            $allowed = config("ehealth.episode_type_encounter_classes.$episodeTypeCode", []);
            if (!in_array($value, $allowed, true)) {
                $fail(__('validation.custom.encounter.classCode.episode_type_forbidden', ['value' => $value]));
            }
        };

        $rules['encounter.classCode'][] = static function (string $attribute, mixed $value, Closure $fail): void {
            $allowed = config('ehealth.legal_entity_encounter_classes.' . legalEntity()->type->name, []);
            if (!in_array($value, $allowed, true)) {
                $fail(__('validation.custom.encounter.classCode.legal_entity_forbidden', ['value' => $value]));
            }
        };
    }

    /**
     * Add allowed values for encounter types.
     *
     * @param  array  $rules
     * @return void
     */
    private function addAllowedEncounterTypes(array &$rules): void
    {
        $rules['encounter.typeCode'][] = function (string $attribute, mixed $value, Closure $fail): void {
            $classCode = $this->encounter['classCode'] ?? null;
            if (empty($classCode)) {
                return;
            }
            $allowed = config("ehealth.encounter_class_encounter_types.$classCode", []);
            if (!in_array($value, $allowed, true)) {
                $fail(__('validation.custom.encounter.typeCode.class_forbidden', ['value' => $value]));
            }
        };
    }

    /**
     * Add condition code system validation based on encounter class.
     *
     * @param  array  $rules
     * @return void
     */
    private function addAllowedConditionCodes(array &$rules): void
    {
        $rules['conditions.*.codeSystem'][] = function (string $attribute, mixed $value, Closure $fail): void {
            $classCode = $this->encounter['classCode'] ?? null;
            if (empty($classCode) || $classCode === 'PHC') {
                return;
            }

            if ($value !== 'eHealth/ICD10_AM/condition_codes') {
                $fail(__('validation.custom.conditions.codeSystem.class_forbidden'));
            }
        };

        $rules['conditions'][] = static function (string $attribute, mixed $value, Closure $fail): void {
            if (empty($value)) {
                return;
            }

            $hasDuplicate = collect($value)->groupBy('codeSystem')
                ->contains(fn (Collection $group) => $group->count() > 1);

            if ($hasDuplicate) {
                $fail(__('validation.custom.conditions.max_one_per_dictionary'));
            }
        };
    }

    /**
     * Validate that conditions requiring a psychiatry evidence reference have a valid condition evidence attached.
     *
     * @param  array  $rules
     * @return void
     */
    private function addPsychiatryEvidenceValidation(array &$rules): void
    {
        $rules['conditions.*'][] = static function (string $attribute, mixed $value, Closure $fail): void {
            $codeCode = data_get($value, 'codeCode');
            $psychiatryCodes = config('ehealth.psychiatry_icpc2_diagnoses_evidence_check', []);

            if (!in_array($codeCode, $psychiatryCodes, true)) {
                return;
            }

            $evidenceDetails = collect(data_get($value, 'evidenceDetails', []));
            $conditionEvidence = $evidenceDetails->firstWhere('type', '=', 'condition');

            if (!$conditionEvidence) {
                $fail(__('validation.custom.conditions.psychiatry_evidence_required', ['code' => $codeCode]));

                return;
            }

            $allowedCodes = config('ehealth.icd10am_speciality_conditions_allowed.PSYCHIATRY', []);

            if (!in_array(data_get($conditionEvidence, 'codeCode'), $allowedCodes, true)) {
                $fail(__('validation.custom.conditions.psychiatry_evidence_code_forbidden', ['code' => $codeCode]));
            }
        };
    }

    /**
     * Validate that ASSISTANT and MED_COORDINATOR employees only use their allowed condition codes.
     *
     * @param  array  $rules
     * @return void
     */
    private function addEmployeeTypeConditionsValidation(array &$rules): void
    {
        $employeeType = Auth::user()->getEncounterWriterEmployee()->employeeType;

        $rules['conditions.*'][] = static function (string $attribute, mixed $value, Closure $fail) use (
            $employeeType
        ): void {
            $allowedByCodeSystem = config("ehealth.employee_type_conditions_allowed.$employeeType");

            if ($allowedByCodeSystem === null) {
                return;
            }

            $codeSystem = data_get($value, 'codeSystem');
            $allowedCodes = $allowedByCodeSystem[$codeSystem] ?? [];
            $codeCode = data_get($value, 'codeCode');

            if (!in_array($codeCode, $allowedCodes, true)) {
                $fail(__("validation.custom.conditions.employee_type_code_forbidden"));
            }
        };
    }

    /**
     * Validate that the asserter's officio speciality is allowed to set the given ICD10_AM condition code.
     * Only applies when primarySource is true and codeSystem is eHealth/ICD10_AM/condition_codes.
     *
     * @param  array  $rules
     * @return void
     */
    private function addSpecialityConditionsValidation(array &$rules): void
    {
        $speciality = Auth::user()
            ->getEncounterWriterEmployee()
            ->loadMissing('specialities')
            ->specialities
            ->firstWhere('speciality_officio', true)
            ->speciality;

        $rules['conditions.*'][] = static function (string $attribute, mixed $value, Closure $fail) use (
            $speciality
        ): void {
            if (data_get($value, 'codeSystem') !== 'eHealth/ICD10_AM/condition_codes') {
                return;
            }

            if (!$speciality) {
                return;
            }

            $allowedCodes = config("ehealth.icd10am_speciality_conditions_allowed.$speciality");
            if ($allowedCodes === null) {
                return;
            }

            $codeCode = data_get($value, 'codeCode');
            if (!in_array($codeCode, $allowedCodes, true)) {
                $fail(__('validation.custom.conditions.speciality_condition_code_forbidden', ['code' => $codeCode]));
            }
        };
    }

    /**
     * @param  string  $attribute
     * @return array
     */
    private function reasonReferenceCodeRule(string $attribute): array
    {
        $parts = explode('.', $attribute);
        $type = $this->procedures[(int)$parts[1]]['reasonReferences'][(int)$parts[3]]['type'] ?? null;

        $dictionaries = match ($type) {
            'observation' => ['eHealth/LOINC/observation_codes', 'eHealth/ICF/classifiers'],
            'condition' => ['eHealth/ICPC2/condition_codes', 'eHealth/ICD10_AM/condition_codes'],
            default => [
                'eHealth/LOINC/observation_codes',
                'eHealth/ICF/classifiers',
                'eHealth/ICPC2/condition_codes',
                'eHealth/ICD10_AM/condition_codes',
            ],
        };

        return ['nullable', 'string', new InDictionary($dictionaries)];
    }

    /**
     * Required if the immunization is from a primary source or the protocol authority is MoH.
     *
     * @param  string  $attribute  e.g. immunizations.0.vaccinationProtocols.1.doseSequence
     * @return RequiredIf
     */
    private function requiredIfProtocolFieldsMandatory(string $attribute): RequiredIf
    {
        $parts = explode('.', $attribute);
        $immunizationIndex = (int)$parts[1];
        $protocolIndex = (int)$parts[3];

        $immunization = $this->immunizations[$immunizationIndex] ?? [];
        $authorityCode = $immunization['vaccinationProtocols'][$protocolIndex]['authorityCode'] ?? null;
        $primarySource = $immunization['primarySource'] ?? null;

        return Rule::requiredIf($authorityCode === 'MoH' || $primarySource === true);
    }
}
