<?php
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        public function up(): void
        {
            Schema::create('adoptions', function (Blueprint $table) {
                $table->id('adoption_id');

                $table->unsignedBigInteger('pet_id');
                $table->unsignedBigInteger('giver_id');
                $table->unsignedBigInteger('adopter_id');
                $table->unsignedBigInteger('request_id');
                $table->unsignedBigInteger('approved_by')->nullable();

                $table->date('adoption_date');

                $table->timestamps();

                // Foreign Keys
                $table->foreign('pet_id')->references('pet_id')->on('pets')->onDelete('cascade');
                $table->foreign('giver_id')->references('user_id')->on('users')->onDelete('cascade');
                $table->foreign('adopter_id')->references('user_id')->on('users')->onDelete('cascade');
                $table->foreign('request_id')->references('request_id')->on('adoption_requests')->onDelete('cascade');
                $table->foreign('approved_by')->references('user_id')->on('users')->onDelete('set null');
            });
        }

        public function down(): void
        {
            Schema::dropIfExists('adoptions');
        }
};
