<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('adoption_requests', function (Blueprint $table) {
            $table->id('request_id');
            $table->foreignId('pet_id')->constrained('pets','pet_id')->onDelete('cascade');
            $table->foreignId('adopter_id')->constrained('users','user_id')->onDelete('cascade');
            $table->enum('status',['pending','accepted','rejected'])->default('pending');
            $table->timestamps();

            $table->unique(['pet_id','adopter_id']); // prevent duplicate requests
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adoption_requests');
    }
};