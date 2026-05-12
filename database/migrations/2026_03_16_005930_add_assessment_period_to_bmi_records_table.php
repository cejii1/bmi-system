<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('bmi_records', 'assessment_period')) {
            Schema::table('bmi_records', function (Blueprint $table) {
                $table->string('assessment_period', 7)->nullable()->after('assessed_date'); // Format: 2026-03
            });
        }

        // Backfill existing records using DB-compatible date formatting
        $driver = DB::getDriverName();
        $dateExpr = match($driver) {
            'sqlite' => "strftime('%Y-%m', assessed_date)",
            default  => "DATE_FORMAT(assessed_date, '%Y-%m')",
        };
        DB::table('bmi_records')
            ->whereNull('assessment_period')
            ->update(['assessment_period' => DB::raw($dateExpr)]);

        // Index for fast lookups by period
        Schema::table('bmi_records', function (Blueprint $table) {
            $table->index('assessment_period');
        });
    }

    public function down(): void
    {
        Schema::table('bmi_records', function (Blueprint $table) {
            $table->dropIndex(['assessment_period']);
            $table->dropColumn('assessment_period');
        });
    }
};
