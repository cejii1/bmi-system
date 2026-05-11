<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('personnel', function (Blueprint $table) {
            $table->string('position_title')->nullable()->after('personnel_type');
            // Make rank and badge_number nullable for NUP
            $table->string('rank')->nullable()->change();
            $table->string('badge_number')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('personnel', function (Blueprint $table) {
            $table->dropColumn('position_title');
            $table->string('rank')->nullable(false)->change();
            $table->string('badge_number')->nullable(false)->change();
        });
    }
};
