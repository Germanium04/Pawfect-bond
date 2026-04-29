<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id('report_id');
            $table->foreignId('reporter_id')->constrained('users','user_id')->onDelete('cascade');
            $table->foreignId('reported_user_id')->nullable()->constrained('users','user_id')->nullOnDelete();
            $table->foreignId('reported_pet_id')->nullable()->constrained('pets','pet_id')->nullOnDelete();
            $table->enum('report_type',['user','post']);
            $table->text('reason');
            $table->enum('status',['pending','suspended','banned'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};