<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id'); // primary key
            $table->string('username')->unique();
            $table->string('first_name',50);
            $table->string('last_name',50);
            $table->string('gender',10)->nullable();
            $table->date('birthdate')->nullable();
            $table->integer('age')->nullable();
            $table->enum('marital_status',['single','married','divorced'])->nullable();
            $table->string('email',100)->unique();
            $table->string('password');
            $table->string('contact_number',11)->nullable();
            $table->text('address')->nullable();
            $table->string('profile_image')->nullable(); // Add this line
            $table->enum('role',['admin','pet_lover'])->default('pet_lover');
            $table->enum('status',['active','suspended','banned'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};