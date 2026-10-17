<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id('id_log');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_nama')->nullable();
            $table->string('user_username')->nullable();
            $table->string('user_level')->nullable();
            $table->string('action', 50); // LOGIN, LOGOUT, CREATE_KONTEN, ARCHIVE_KONTEN, dll
            $table->text('description');
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
