<?php

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
        Schema::table('bmi_records', function (Blueprint $table) {
            $table->string('photo_front')->nullable()->after('waist_hip_ratio');
            $table->string('photo_right')->nullable()->after('photo_front');
            $table->string('photo_left')->nullable()->after('photo_right');
        });
    }

    public function down(): void
    {
        Schema::table('bmi_records', function (Blueprint $table) {
            $table->dropColumn(['photo_front', 'photo_right', 'photo_left']);
        });
    }
};
