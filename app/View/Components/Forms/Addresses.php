<?php

declare(strict_types=1);

namespace App\View\Components\Forms;

use App\Classes\eHealth\EHealth;
use App\Traits\FormTrait;
use App\Exceptions\EHealth\EHealthConnectionException;
use App\Exceptions\EHealth\EHealthException;
use Illuminate\View\Component;

abstract class Addresses extends Component
{
    use FormTrait;

    public bool $readonly;

    public array $address = [];

    public ?array $regions = [];

    public array $districts = [];

    public ?array $settlements = [];

    public ?array $streets = [];

    public string $class = '';

    /**
     * Create a new component instance.
     */
    public function __construct($address, $districts, $settlements, $streets, $class, $readonly = false)
    {
        $this->readonly = $readonly;

        $this->address = $address;

        try {
            $this->regions = cache()->remember('ehealth_regions', now()->addWeek(), function () {
                return EHealth::address()->getRegions()->getData();
            });
        } catch (EHealthException|EHealthConnectionException $exception) {
            $exception->handle('Error when searching for regions');

            $this->regions = [
                ['name' => 'АР КРИМ'],
                ['name' => 'ВІННИЦЬКА ОБЛАСТЬ'],
                ['name' => 'ВОЛИНСЬКА ОБЛАСТЬ'],
                ['name' => 'ДНІПРОПЕТРОВСЬКА ОБЛАСТЬ'],
                ['name' => 'ДОНЕЦЬКА ОБЛАСТЬ'],
                ['name' => 'ЖИТОМИРСЬКА ОБЛАСТЬ'],
                ['name' => 'ЗАКАРПАТСЬКА ОБЛАСТЬ'],
                ['name' => 'ЗАПОРІЗЬКА ОБЛАСТЬ'],
                ['name' => 'ІВАНО-ФРАНКІВСЬКА ОБЛАСТЬ'],
                ['name' => 'КИЇВСЬКА ОБЛАСТЬ'],
                ['name' => 'КІРОВОГРАДСЬКА ОБЛАСТЬ'],
                ['name' => 'ЛУГАНСЬКА ОБЛАСТЬ'],
                ['name' => 'ЛЬВІВСЬКА ОБЛАСТЬ'],
                ['name' => 'М.КИЇВ'],
                ['name' => 'М.СЕВАСТОПОЛЬ'],
                ['name' => 'МИКОЛАЇВСЬКА ОБЛАСТЬ'],
                ['name' => 'ОДЕСЬКА ОБЛАСТЬ'],
                ['name' => 'ПОЛТАВСЬКА ОБЛАСТЬ'],
                ['name' => 'РІВНЕНСЬКА ОБЛАСТЬ'],
                ['name' => 'СУМСЬКА ОБЛАСТЬ'],
                ['name' => 'ТЕРНОПІЛЬСЬКА ОБЛАСТЬ'],
                ['name' => 'ХАРКІВСЬКА ОБЛАСТЬ'],
                ['name' => 'ХЕРСОНСЬКА ОБЛАСТЬ'],
                ['name' => 'ХМЕЛЬНИЦЬКА ОБЛАСТЬ'],
                ['name' => 'ЧЕРКАСЬКА ОБЛАСТЬ'],
                ['name' => 'ЧЕРНІВЕЦЬКА ОБЛАСТЬ'],
                ['name' => 'ЧЕРНІГІВСЬКА ОБЛАСТЬ'],
            ];
        }

        $this->districts = $districts;

        $this->settlements = $settlements;

        $this->streets = $streets;

        $this->class = $class;

        $this->dictionaries = dictionary()->basics()->getMultipleFormatted(['SETTLEMENT_TYPE', 'STREET_TYPE'])->toArray();
    }

    abstract public static function getAddressRules(array $address): array;

    abstract public static function getAddressMessages(): array;
}
