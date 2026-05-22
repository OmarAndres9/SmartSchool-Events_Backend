<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('_recursos__table', function (Blueprint $table) {
            $table->integer('capacidad')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('_recursos__table', function (Blueprint $table) {
            $table->string('capacidad', 50)->nullable()->change();
        });
    }
};
