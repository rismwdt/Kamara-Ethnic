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
        Schema::create('performers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('gender', ['laki-laki', 'perempuan', 'lainnya'])->nullable();
            $table->foreignId('performer_role_id')->constrained('performer_roles')->onDelete('cascade'); // relasi ke tabel peran
            $table->boolean('is_active')->default(true);
            $table->string('phone')->nullable();
            $table->string('account_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->integer('experience')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performers');
    }
};
