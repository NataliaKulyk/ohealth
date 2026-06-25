<div
    x-show="showCertificate"
    style="display:none"
    @keydown.escape.window.prevent.stop="showCertificate = false"
    role="dialog"
    aria-modal="true"
    class="modal"
>
    <div x-transition.opacity class="fixed inset-0 bg-black/40"></div>

    <div x-transition @click="showCertificate = false" class="modal-wrapper">

        <div @click.stop x-trap.noscroll.inert="showCertificate"
             class="modal-content w-full max-w-2xl mx-auto bg-white dark:bg-gray-900 rounded-xl shadow-2xl"
        >
            <div id="certificate-print-area" class="p-8">

                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                    Інформаційна довідка
                </h2>

                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Неідентифікований пацієнт</p>
                <p class="text-sm font-mono text-gray-900 dark:text-white mb-5 uppercase tracking-wide">
                    {{ $uuid }}
                </p>

                <div class="flex items-center justify-center mb-6">
                    <div class="relative w-full h-20 overflow-hidden rounded">
                        <svg viewBox="0 0 400 80" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                            @php
                                $chars = str_split(preg_replace('/[^a-f0-9]/i', '', $uuid));
                                $x = 0;
                            @endphp
                            @foreach($chars as $i => $char)
                                @php
                                    $val  = hexdec($char);
                                    $w    = max(2, $val * 1.6);
                                    $gap  = ($i % 3 === 0) ? 3 : 1.5;
                                @endphp
                                <rect x="{{ $x }}" y="0" width="{{ $w }}" height="80" fill="#111827"/>
                                @php $x += $w + $gap; @endphp
                            @endforeach
                        </svg>
                    </div>
                </div>

                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-3">Основна інформація</h3>
                <table class="w-full text-sm border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden mb-6">
                    <tbody>
                        @php
                            $rows = [
                                ["ІДЕНТИФІКАТОР ПАЦІЄНТА В ЗАКЛАДІ ОХОРОНИ ЗДОРОВ'Я:", $taxId ?? '-'],
                                ["ІМ'Я ПАЦІЄНТА:", $firstName ?? '-'],
                                ["ПРІЗВИЩЕ ПАЦІЄНТА:", $lastName ?? '-'],
                                ["ПО-БАТЬКОВІ ПАЦІЄНТА:", $secondName ?? '-'],
                                ["СТАТЬ:", $gender === 'FEMALE' ? 'Жіноча' : 'Чоловіча'],
                                ["ДАТА НАРОДЖЕННЯ:", $birthDate ?? '-'],
                                ["ІМ'Я КОНТАКТНОЇ ОСОБИ:", $emergencyContact['firstName'] ?? '-'],
                                ["ПРІЗВИЩЕ КОНТАКТНОЇ ОСОБИ:", $emergencyContact['lastName'] ?? '-'],
                                ["ПО-БАТЬКОВІ КОНТАКТНОЇ ОСОБИ:", $emergencyContact['secondName'] ?? '-'],
                            ];
                        @endphp
                        @foreach($rows as [$label, $value])
                            <tr class="border-b border-gray-200 dark:border-gray-700 last:border-0">
                                <td class="px-4 py-2.5 text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 w-1/2 bg-gray-50 dark:bg-gray-800">
                                    {{ $label }}
                                </td>
                                <td class="px-4 py-2.5 text-gray-900 dark:text-white">
                                    {{ $value }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-3">
                    Контактні телефони для екстреного зв'язку
                </h3>
                <table class="w-full text-sm border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                    <tbody>
                        @php
                            $emergencyPhones = $emergencyContact['phones'] ?? [];
                            $phoneTypeLabels = [
                                'MOBILE' => 'МОБІЛЬНИЙ НОМЕР ТЕЛЕФОНА:',
                                'LAND_LINE' => 'ДОМАШНІЙ НОМЕР ТЕЛЕФОНА:',
                            ];
                        @endphp
                        @forelse($emergencyPhones as $phone)
                            <tr class="border-b border-gray-200 dark:border-gray-700 last:border-0">
                                <td class="px-4 py-2.5 text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 w-1/2 bg-gray-50 dark:bg-gray-800">
                                    {{ $phoneTypeLabels[$phone['type'] ?? 'MOBILE'] ?? strtoupper($phone['type'] ?? 'ТЕЛЕФОН') . ':' }}
                                </td>
                                <td class="px-4 py-2.5 text-gray-900 dark:text-white">
                                    {{ $phone['number'] ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-4 py-2.5 text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800">
                                    Дані відсутні
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex items-center gap-3 px-8 pb-6 border-t border-gray-100 dark:border-gray-800 pt-4"
                 x-data="{
                     printCertificate() {
                         const area = document.getElementById('certificate-print-area');
                         const win  = window.open('', '_blank', 'width=800,height=900');
                         win.document.write(`
                             <!DOCTYPE html>
                             <html lang='uk'>
                             <head>
                                 <meta charset='UTF-8'>
                                 <title>Інформаційна довідка</title>
                                 <style>
                                     * { box-sizing: border-box; margin: 0; padding: 0; }
                                     body { font-family: Arial, sans-serif; font-size: 13px; color: #111; background: #fff; padding: 32px; }
                                     h2 { font-size: 18px; font-weight: 700; margin-bottom: 12px; }
                                     h3 { font-size: 14px; font-weight: 700; margin: 20px 0 8px; }
                                     p  { font-size: 12px; color: #555; margin-bottom: 4px; }
                                     svg { width: 100%; height: 80px; display: block; margin-bottom: 20px; }
                                     table { width: 100%; border-collapse: collapse; margin-bottom: 20px; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; }
                                     tr { border-bottom: 1px solid #e5e7eb; }
                                     tr:last-child { border-bottom: none; }
                                     td { padding: 8px 14px; font-size: 12px; vertical-align: top; }
                                     td:first-child { background: #f9fafb; font-weight: 600; text-transform: uppercase; color: #6b7280; font-size: 10px; width: 50%; }
                                     @media print {
                                         body { padding: 20px; }
                                         @page { margin: 15mm; }
                                     }
                                 </style>
                             </head>
                             <body>` + area.innerHTML + `</body></html>`
                         );
                         win.document.close();
                         win.focus();
                         setTimeout(() => { win.print(); }, 400);
                     }
                 }"
            >
                <button type="button"
                        class="button-minor"
                        @click="showCertificate = false"
                >
                    Закрити
                </button>
                <button type="button"
                        class="button-primary-outline flex items-center gap-2"
                        @click="printCertificate()"
                >
                    @icon('printer', 'w-4 h-4')
                    <span>Надрукувати</span>
                </button>
            </div>
        </div>
    </div>
</div>
