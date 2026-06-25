<div
    x-data="{
        showEditModal: false,
        editPatient: {
            uuid: '',
            firstName: '',
            lastName: '',
            secondName: '',
            gender: 'MALE',
            birthDate: '',
            contactFirstName: '',
            contactLastName: '',
            contactSecondName: '',
            contactPhoneType: 'MOBILE',
            contactPhone: '',
        }
    }"
    @open-edit-patient.window="
        editPatient = $event.detail;
        showEditModal = true;
    "
>
    <template x-teleport="body">
        <div
            x-show="showEditModal"
            style="display:none"
            @keydown.escape.window.prevent.stop="showEditModal = false"
            role="dialog"
            aria-modal="true"
            class="modal"
        >
            <div x-transition.opacity class="fixed inset-0 bg-black/40"></div>

            <div x-transition @click="showEditModal = false" class="modal-wrapper">

                <div @click.stop x-trap.noscroll.inert="showEditModal"
                     class="modal-content w-full max-w-2xl mx-auto bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden"
                >
                    <div class="p-8 space-y-6">

                        <div>
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                                Редагування даних пацієнта
                            </h2>
                            <p class="mt-1 text-sm font-mono text-gray-500 dark:text-gray-400 uppercase tracking-wide"
                               x-text="'ID ' + editPatient.uuid"
                            ></p>
                        </div>

                        <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                            <div class="px-5 py-3 bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Основна інформація</h3>
                            </div>
                            <div class="p-5 space-y-5">

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="form-group group">
                                        <input type="text"
                                               class="input peer"
                                               placeholder=" "
                                               x-model="editPatient.firstName"
                                        />
                                        <label class="label">Ім'я пацієнта</label>
                                    </div>
                                    <div class="form-group group">
                                        <input type="text"
                                               class="input peer"
                                               placeholder=" "
                                               x-model="editPatient.lastName"
                                        />
                                        <label class="label">Прізвище пацієнта</label>
                                    </div>
                                    <div class="form-group group">
                                        <input type="text"
                                               class="input peer"
                                               placeholder=" "
                                               x-model="editPatient.secondName"
                                        />
                                        <label class="label">По-батькові пацієнта</label>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="form-group group">
                                        <select class="input-select peer w-full"
                                                x-model="editPatient.gender"
                                        >
                                            <option value="MALE">Чоловіча</option>
                                            <option value="FEMALE">Жіноча</option>
                                        </select>
                                        <label class="label scale-75 -translate-y-6">Стать</label>
                                    </div>
                                    <div class="form-group group">
                                        <input type="text"
                                               class="input peer"
                                               placeholder=" "
                                               x-model="editPatient.birthDate"
                                        />
                                        <label class="label">Дата народження</label>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                            <div class="px-5 py-3 bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Контактна особа</h3>
                            </div>
                            <div class="p-5 space-y-5">

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="form-group group">
                                        <input type="text"
                                               class="input peer"
                                               placeholder=" "
                                               x-model="editPatient.contactFirstName"
                                        />
                                        <label class="label">Ім'я</label>
                                    </div>
                                    <div class="form-group group">
                                        <input type="text"
                                               class="input peer"
                                               placeholder=" "
                                               x-model="editPatient.contactLastName"
                                        />
                                        <label class="label">Прізвище</label>
                                    </div>
                                    <div class="form-group group">
                                        <input type="text"
                                               class="input peer"
                                               placeholder=" "
                                               x-model="editPatient.contactSecondName"
                                        />
                                        <label class="label">По-батькові</label>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="form-group group">
                                        <select class="input-select peer w-full"
                                                x-model="editPatient.contactPhoneType"
                                        >
                                            <option value="MOBILE">Мобільний</option>
                                            <option value="LAND_LINE">Стаціонарний</option>
                                        </select>
                                        <label class="label scale-75 -translate-y-6">Тип телефону</label>
                                    </div>
                                    <div class="form-group group">
                                        <input type="text"
                                               class="input peer"
                                               placeholder=" "
                                               x-model="editPatient.contactPhone"
                                        />
                                        <label class="label">Телефон</label>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <button type="button"
                                    class="button-minor"
                                    @click="showEditModal = false"
                            >
                                Назад
                            </button>
                            <button type="button"
                                    class="button-primary"
                                    @click="showEditModal = false"
                            >
                                Зберегти зміни
                            </button>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </template>
</div>
