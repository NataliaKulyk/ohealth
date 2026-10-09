<?php
$content = file_get_contents('resources/views/livewire/encounter/parts/encounter-eprescription-drawer.blade.php');
$startPattern = '/@if \(\$showEncounterEPrescriptionDrawer\).*?<h2[^>]*>.*?<\/h2>/is';
$startReplace = '@if ($showEncounterEPrescriptionDrawer)
    <div x-data="{ open: true }">
        <x-dialog-drawer
            x-model="open"
            maxWidth="4/5"
            overlayWidth="100%"
            stopClickPropagation="true"
            onCloseClick="$wire.closeEncounterEPrescriptionDrawer()"
        >
            <x-slot name="title">Електронний рецепт поза енкаунтером</x-slot>
            <div class="px-6 pb-6 pt-2">';
$content = preg_replace($startPattern, $startReplace, $content);

$endPattern = '/<\/form>\s*<\/div>\s*@endif/is';
$endReplace = '</form>
            </div>
        </x-dialog-drawer>
    </div>
@endif';
$content = preg_replace($endPattern, $endReplace, $content);

file_put_contents('resources/views/livewire/encounter/parts/encounter-eprescription-drawer.blade.php', $content);
echo "Updated eprescription drawer\n";

$contentRef = file_get_contents('resources/views/livewire/encounter/parts/encounter-referral-drawer.blade.php');
$startPatternRef = '/@if \(\$showEncounterReferralDrawer\).*?<h3[^>]*>.*?<\/h3>/is';
$startReplaceRef = '@if ($showEncounterReferralDrawer)
    <div x-data="{ open: true, openServiceCatalog: false }" @encounter-referral-service-catalog-close.window="openServiceCatalog = false">
        <x-dialog-drawer
            x-model="open"
            maxWidth="4/5"
            overlayWidth="100%"
            stopClickPropagation="true"
            onCloseClick="$wire.closeEncounterReferralDrawer()"
        >
            <x-slot name="title">Виписати електронне направлення (поза енкаунтером)</x-slot>
            <div class="px-6 pb-6 pt-2">';
$contentRef = preg_replace($startPatternRef, $startReplaceRef, $contentRef);

$endPatternRef = '/<\/div>\s*@endif\s*$/is';
$endReplaceRef = '        </div>
        </x-dialog-drawer>
    </div>
@endif';
$contentRef = preg_replace($endPatternRef, $endReplaceRef, $contentRef);

file_put_contents('resources/views/livewire/encounter/parts/encounter-referral-drawer.blade.php', $contentRef);
echo "Updated referral drawer\n";
