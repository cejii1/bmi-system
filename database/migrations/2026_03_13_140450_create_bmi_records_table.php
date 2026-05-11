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
        Schema::create('bmi_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personnel_id')->constrained('personnel')->onDelete('cascade');
            $table->integer('age');
            $table->decimal('height', 5, 2);
            $table->decimal('weight', 5, 2);
            $table->decimal('waist', 5, 2)->nullable();
            $table->decimal('wrist', 5, 2)->nullable();
            $table->decimal('hip', 5, 2)->nullable();
            $table->decimal('bmi_value', 5, 2);
            $table->string('bmi_category');
            $table->decimal('weight_to_lose', 5, 2)->nullable();
            $table->decimal('normal_weight_min', 5, 2);
            $table->decimal('normal_weight_max', 5, 2);
            $table->string('body_frame')->nullable();
            $table->decimal('waist_hip_ratio', 5, 2)->nullable();
            $table->date('assessed_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bmi_records');
    }
};
