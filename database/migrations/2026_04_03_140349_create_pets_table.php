<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pets', function (Blueprint $table) {
            $table->id('pet_id');
            $table->foreignId('owner_id')->constrained('users','user_id')->onDelete('cascade'); // reference correct PK
            $table->string('name',50);
            $table->string('breed',50);
            $table->enum('gender',['Male','Female']);
            $table->date('birthday');
            $table->text('likes');
            $table->text('dislikes');
            $table->text('personality');
            $table->string('pet_image')->nullable(); // Add this line
            $table->enum('status',['available','pending','rehomed','removed'])->default('available');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};