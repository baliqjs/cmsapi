<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('konten', function (Blueprint $table) {
            if (!Schema::hasColumn('konten', 'status')) {
                $table->enum('status', ['Published', 'Archived'])->default('Published')->after('foto');
            }
        });
    }

    public function down(): void
    {
        Schema::table('konten', function (Blueprint $table) {
            if (Schema::hasColumn('konten', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
