<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Person\Gender;
use App\Enums\Preperson\Status;
use Eloquence\Behaviours\HasCamelCasing;
use Illuminate\Database\Eloquent\Model;

class Preperson extends Model
{
    use HasCamelCasing;

    protected $table = 'prepersons';

    protected $fillable = [
        'first_name',
        'last_name',
        'second_name',
        'gender',
        'birth_date',
        'emergency_contact',
        'death_date',
        'note',
        'status',
        'ehealth_inserted_at',
        'ehealth_inserted_by',
        'ehealth_updated_at',
        'ehealth_updated_by'
    ];

    protected $hidden = [
        'id',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'gender' => Gender::class,
        'emergency_contact' => 'array',
        'status' => Status::class
    ];

    /**
     * Generate a random external_id matching the eHealth pattern `^[0-9]{8,10}\.[0-9]{8,10}\.[0-9]{1,10}$`.
     * Uniqueness is enforced by the unique index on the column, not by this generator.
     *
     * @return string
     */
    public static function generateExternalId(): string
    {
        return sprintf(
            '%d.%d.%d',
            random_int(10_000_000, 9_999_999_999),
            random_int(10_000_000, 9_999_999_999),
            random_int(1, 9_999_999_999)
        );
    }
}
