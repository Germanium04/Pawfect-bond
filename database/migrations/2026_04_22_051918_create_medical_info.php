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
      Schema::create('pet_medical_records', function (Blueprint $table) {
        $table->id();

        $table->foreignId('pet_id')
            ->constrained('pets', 'pet_id')
            ->cascadeOnDelete();

        $table->boolean('vaccinated')->default(false);
        $table->date('vaccinated_date')->nullable();

        $table->boolean('dewormed')->default(false);
        $table->date('dewormed_date')->nullable();

        $table->boolean('neutered')->default(false);
        $table->date('neutered_date')->nullable();

        $table->string('vaccinated_certificate')->nullable();
        $table->string('dewormed_certificate')->nullable();
        $table->string('neutered_certificate')->nullable();

        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pet_medical_records');
    }
};