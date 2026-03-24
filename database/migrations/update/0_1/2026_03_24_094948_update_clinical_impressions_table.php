<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('clinical_impressions', function (Blueprint $table) {
            $table->foreignId('person_id')->after('uuid')->constrained('persons');
            $table->string('explanatory_letter')->after('note')->nullable();
            $table->timestamp('ehealth_inserted_at')->nullable()->after('note');
            $table->timestamp('ehealth_updated_at')->nullable()->after('ehealth_inserted_at');
            $table->foreignId('encounter_internal_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clinical_impressions', function (Blueprint $table) {
            $table->foreignId('encounter_internal_id')->nullable(false)->change();
            $table->dropColumn(['explanatory_letter', 'ehealth_updated_at', 'ehealth_inserted_at']);
            $table->dropForeign(['person_id']);
            $table->dropColumn('person_id');
        });
    }
};
