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
        Schema::create('booking_performers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('performer_id')->constrained()->onDelete('cascade');
            $table->boolean('is_external')->default(false);
            $table->enum('confirmation_status', ['pending','confirmed','declined','cancelled'])
                    ->default('confirmed');
            $table->decimal('agreed_rate', 12, 2)->nullable();
            $table->timestamps();

            $table->unique(['booking_id', 'performer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_performers');
    }
};
