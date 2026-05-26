<div class="relative"> {{-- This required for table overflow scrolling --}}
    <fieldset class="fieldset"
              x-data="{
                  openModal: false,
                  selectedType: 'condition',
                  searchQuery: '',
                  isLoading: false,
                  searchResults: [],

                  init() {
                      this.$watch('selectedType', () => this.fetchRecords());
                      this.$watch('openModal', (val) => {
                          if (val) {
                              this.selectedType = 'condition';
                              this.searchQuery = '';
                              this.searchResults = [];
                              this.fetchRecords();
                          }
                      });
                  },
                  fetchRecords() {
                      if (!this.selectedType) {
                          this.searchResults = [];
                          return;
                      }
                      this.isLoading = true;
                      $wire.searchConditionsOrObservations(this.selectedType)
                          .then(() => {
                              this.searchResults = JSON.parse(JSON.stringify($wire.evidenceDetails || []));
                          })
                          .finally(() => {
                              this.isLoading = false;
                          });
                  },
                  filteredRecords() {
                      return this.searchResults.filter(rec => {
                          const dictionaryName = this.selectedType === 'condition'
                              ? 'eHealth/ICPC2/condition_codes'
                              : 'eHealth/LOINC/observation_codes';
                          const name = $wire.dictionaries[dictionaryName]?.[rec.codeCode] || '';
                          const label = (rec.codeCode + ' ' + name).toLowerCase();
                          if (this.searchQuery) {
                              const query = this.searchQuery.toLowerCase();
                              return label.includes(query);
                          }
                          return true;
                      });
                  },
                  addEvidence(record) {
                      const existingIds = modalCondition.evidenceDetails.map(detail => detail.id);
                      if (!existingIds.includes(record.id)) {
                          modalCondition.evidenceDetails.push({
                              id: record.id,
                              ehealthInsertedAt: record.ehealthInsertedAt,
                              codeCode: record.codeCode,
                              type: this.selectedType
                          });
                      }
                      this.openModal = false;
                  }
              }"
    >
        <legend class="legend">
            <h2>{{ __('patients.evidence') }}</h2>
        </legend>

        <table class="table-input w-inherit">
            <thead class="thead-input">
            <tr>
                <th scope="col" class="th-input">{{ __('forms.date') }}</th>
                <th scope="col" class="th-input">{{ __('patients.code_and_name') }}</th>
                <th scope="col" class="th-input">{{ __('forms.action') }}</th>
            </tr>
            </thead>
            <tbody>
            <template x-for="(detail, index) in modalCondition.evidenceDetails">
                <tr>
                    <td class="td-input"
                        x-text="detail.ehealthInsertedAt || ''"
                    ></td>
                    <td class="td-input"
                        x-text="`${ detail.codeCode } - ${
                            $wire.dictionaries['eHealth/LOINC/observation_codes'][detail.codeCode] ||
                            $wire.dictionaries['eHealth/ICF/classifiers'][detail.codeCode] ||
                            $wire.dictionaries['eHealth/ICD10_AM/condition_codes'][detail.codeCode] ||
                            $wire.dictionaries['eHealth/ICPC2/condition_codes'][detail.codeCode]
                        }`"
                    ></td>
                    <td class="td-input">
                        {{-- That all that is needed for the dropdown --}}
                        <div x-data="{
                                 openDropdown: false,
                                 toggle() {
                                     if (this.openDropdown) {
                                         return this.close();
                                     }

                                     this.$refs.button.focus();

                                     this.openDropdown = true;
                                 },
                                 close(focusAfter) {
                                     if (!this.openDropdown) return;

                                     this.openDropdown = false;

                                     focusAfter && focusAfter.focus()
                                 }
                             }"
                             @keydown.escape.prevent.stop="close($refs.button)"
                             @focusin.window="!$refs.panel.contains($event.target) && close()"
                             x-id="['dropdown-button']"
                             class="relative"
                        >
                            {{-- Dropdown Button --}}
                            <button x-ref="button"
                                    @click="toggle()"
                                    :aria-expanded="openDropdown"
                                    :aria-controls="$id('dropdown-button')"
                                    type="button"
                            >
                                <svg class="w-6 h-6 text-gray-800 dark:text-gray-200 cursor-pointer" aria-hidden="true"
                                     xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                     viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="square" stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M7 19H5a1 1 0 0 1-1-1v-1a3 3 0 0 1 3-3h1m4-6a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm7.441 1.559a1.907 1.907 0 0 1 0 2.698l-6.069 6.069L10 19l.674-3.372 6.07-6.07a1.907 1.907 0 0 1 2.697 0Z"/>
                                </svg>
                              </button>

                              {{-- Dropdown Panel --}}
                              <div class="absolute" style="left: 50%"> {{-- Center a dropdown panel --}}
                                  <div x-ref="panel"
                                       x-show="openDropdown"
                                       x-transition.origin.top.left
                                       @click.outside="close($refs.button)"
                                       :id="$id('dropdown-button')"
                                       x-cloak
                                       class="dropdown-panel relative"
                                       style="left: -50%" {{-- Center a dropdown panel --}}
                                  >
                                      <button @click.prevent="modalCondition.evidenceDetails.splice(index, 1); close($refs.button);"
                                              class="dropdown-button dropdown-delete"
                                      >
                                          {{ __('forms.delete') }}
                                      </button>
                                  </div>
                              </div>
                          </div>
                      </td>
                  </tr>
              </template>
              </tbody>
          </table>

          <div>
              {{-- Button to trigger the drawer --}}
              <button @click.prevent="openModal = true"
                      class="item-add my-5"
              >
                  {{ __('forms.add') }}
              </button>

              {{-- Modal/Drawer --}}
              <template x-teleport="body">
                  <div x-show="openModal"
                       x-transition:enter="transition ease-out duration-300"
                       x-transition:enter-start="opacity-0"
                       x-transition:enter-end="opacity-100"
                       x-transition:leave="transition ease-in duration-200"
                       x-transition:leave-start="opacity-100"
                       x-transition:leave-end="opacity-0"
                       x-cloak
                       class="fixed inset-0"
                       style="z-index: 46;"
                       role="dialog"
                       aria-modal="true"
                  >
                      <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm cursor-pointer"
                           aria-hidden="true"
                           @click="openModal = false"
                      ></div>

                      <div id="references-selection-drawer-right"
                           x-show="openModal"
                           x-transition:enter="transition ease-out duration-300"
                           x-transition:enter-start="translate-x-full"
                           x-transition:enter-end="translate-x-0"
                           x-transition:leave="transition ease-in duration-200"
                           x-transition:leave-start="translate-x-0"
                           x-transition:leave-end="translate-x-full"
                           class="absolute top-0 right-0 h-screen pt-20 p-6 bg-white dark:bg-gray-800 shadow-2xl flex flex-col justify-between border-l border-gray-100 dark:border-gray-700 overflow-y-auto"
                           style="z-index: 47; width: calc(80% - 45px); max-width: 885px;"
                           tabindex="-1"
                      >
                          <div class="flex-1 flex flex-col min-h-0">
                              {{-- Title --}}
                              <div class="mb-6">
                                  <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                                      {{ __('patients.add_observations_reports_conditions') }}
                                  </h2>
                              </div>

                              {{-- Search and filters row --}}
                              <div class="form-row-3 mb-6">
                                  <div class="form-group group">
                                      <div class="relative">
                                          <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-3">
                                              @icon('search-outline', 'w-5 h-5 text-gray-400')
                                          </div>
                                          <input type="text"
                                                 x-model="searchQuery"
                                                 class="input with-leading-icon peer w-full"
                                                 placeholder=" "
                                                 id="drawerSearchQuery"
                                          />
                                          <label for="drawerSearchQuery" class="wrapped-label">
                                              {{ __('forms.search') }}
                                          </label>
                                      </div>
                                  </div>

                                  <div class="form-group group">
                                      <select x-model="selectedType"
                                              id="drawerSelectedType"
                                              class="input-select peer w-full"
                                      >
                                          <option value="condition">{{ __('patients.condition_or_diagnosis') }}</option>
                                          <option value="observation">{{ __('patients.medical_observation') }}</option>
                                      </select>
                                      <label for="drawerSelectedType" class="label">
                                          {{ __('forms.type') }}
                                      </label>
                                  </div>
                              </div>

                              <div class="flex-1 overflow-y-auto min-h-0 mb-6 pr-1 relative">
                                  <div x-show="isLoading" class="absolute inset-0 flex items-center justify-center bg-white/70 dark:bg-gray-800/70 z-10" x-cloak>
                                      <x-forms.loading/>
                                  </div>

                                  <table class="table-input w-inherit">
                                      <thead class="thead-input">
                                          <tr>
                                              <th scope="col" class="th-input">{{ __('forms.date') }}</th>
                                              <th scope="col" class="th-input">{{ __('forms.type') }}</th>
                                              <th scope="col" class="th-input">{{ __('patients.code_and_name') }}</th>
                                              <th scope="col" class="th-input text-center">{{ __('forms.action') }}</th>
                                          </tr>
                                      </thead>
                                      <tbody>
                                          <template x-for="record in filteredRecords()" :key="record.id">
                                              <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors">
                                                  <td class="td-input text-[14px] text-gray-900 dark:text-gray-300" x-text="record.ehealthInsertedAt || ''"></td>
                                                  <td class="td-input text-[14px] text-gray-900 dark:text-gray-300" x-text="selectedType === 'condition' ? '{{ __('patients.condition_or_diagnosis') }}' : '{{ __('patients.medical_observation') }}'"></td>
                                                  <td class="td-input text-[14px] text-gray-900 dark:text-white" x-text="`${ record.codeCode } - ${
                                                      $wire.dictionaries[selectedType === 'condition' ? 'eHealth/ICPC2/condition_codes' : 'eHealth/LOINC/observation_codes']?.[record.codeCode] || ''
                                                  }`"></td>
                                                  <td class="td-input text-center">
                                                      <button type="button"
                                                              @click="addEvidence(record)"
                                                              class="inline-flex items-center justify-center text-gray-900 hover:text-blue-600 dark:text-white dark:hover:text-blue-400 font-medium text-sm transition-colors cursor-pointer"
                                                      >
                                                          @icon('plus', 'w-5 h-5')
                                                      </button>
                                                  </td>
                                              </tr>
                                          </template>
                                      </tbody>
                                  </table>

                                  <div x-show="!isLoading && filteredRecords().length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400" x-cloak>
                                      {{ __('forms.nothing_found') }}
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </template>
          </div>
      </fieldset>
  </div>
