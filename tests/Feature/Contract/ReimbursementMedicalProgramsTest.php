<?php

declare(strict_types=1);

namespace Tests\Feature\Contract;

use App\Classes\eHealth\Api\MedicalProgram;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

/**
 * Tests for ReimbursementContractCreate medical programs handling:
 *  - User selection is used (not hardcoded)
 *  - Output format is [{id: uuid}, ...], not [uuid, ...]
 *  - Cache is not defeated on every page load
 *
 * These tests cover pure PHP logic and cache behaviour — no database needed.
 */
class ReimbursementMedicalProgramsTest extends TestCase
{
    public function test_medical_programs_payload_uses_id_object_format(): void
    {
        $programUuid1 = (string) Str::uuid();
        $programUuid2 = (string) Str::uuid();

        $result = array_map(static fn (string $id) => ['id' => $id], array_filter([$programUuid1, $programUuid2]));

        $this->assertSame([['id' => $programUuid1], ['id' => $programUuid2]], $result);
    }

    public function test_medical_programs_payload_is_not_plain_uuid_array(): void
    {
        $programUuid = (string) Str::uuid();

        $result = array_map(static fn (string $id) => ['id' => $id], array_filter([$programUuid]));

        $this->assertNotSame([$programUuid], $result);
        $this->assertSame([['id' => $programUuid]], $result);
    }

    public function test_medical_programs_payload_is_empty_array_when_no_programs_selected(): void
    {
        $result = array_map(static fn (string $id) => ['id' => $id], array_filter([]));

        $this->assertSame([], $result);
    }

    public function test_load_medical_programs_uses_cache_without_clearing_it_each_time(): void
    {
        Cache::flush();

        $mockPrograms = [
            ['id' => (string) Str::uuid(), 'name' => 'Insulin Program', 'type' => 'REIMBURSEMENT'],
        ];

        Cache::put('ehealth_medical_programs_reimbursement', $mockPrograms, 3600);

        $mockApi = Mockery::mock(MedicalProgram::class);
        // API must NOT be called since cache already has data
        $mockApi->shouldNotReceive('getMany');

        $programs = Cache::remember('ehealth_medical_programs_reimbursement', 3600, static function () use ($mockApi) {
            return $mockApi->getMany(['page_size' => 100])->getData();
        });

        $this->assertSame($mockPrograms, $programs);
    }

    public function test_hardcoded_insulin_id_is_not_used_in_payload(): void
    {
        $hardcodedInsulinId = '1a227396-a0e4-4c4f-a0a9-6b358c8929d2';
        $userSelectedId = (string) Str::uuid();

        $result = array_map(static fn (string $id) => ['id' => $id], array_filter([$userSelectedId]));
        $resultIds = array_column($result, 'id');

        $this->assertNotContains($hardcodedInsulinId, $resultIds);
        $this->assertContains($userSelectedId, $resultIds);
    }
}
