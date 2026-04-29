<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id('notification_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade'); // recipient
            $table->string('type');        // adoption_request, adoption_accepted, adoption_declined, new_message, reported, admin_action
            $table->string('title');
            $table->text('message');
            $table->unsignedBigInteger('related_id')->nullable();   // pet_id, request_id, message_id, report_id
            $table->string('related_type')->nullable();             // pet, adoption_request, message, report
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};