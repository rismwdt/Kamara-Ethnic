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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code', 15)->unique();
            $table->string('event_type', 30)->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('price')->default(0);
            $table->unsignedBigInteger('dp')->default(0);
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->text('location_detail');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('client_name');
            $table->string('event_name', 120)->nullable();
            $table->string('male_parents')->nullable();
            $table->string('female_parents')->nullable();
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('nuance')->nullable();
            $table->string('location_photo')->nullable();
            $table->string('image')->nullable();
            $table->string('description', 500)->nullable();
            $table->text('notes')->nullable();
            $table->enum('priority', ['normal', 'darurat'])->default('normal');
            $table->boolean('is_family')->default(false);
            $table->enum('status', ['tertunda','diterima','ditolak','selesai'])->default('tertunda');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
