<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weather', function (Blueprint $table): void {
            $table->id();
            $table->string('city', 120)->index();
            $table->integer('temperature');
            $table->timestamps();
            $table->index('created_at');

            $table->unique(['city', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weather');
    }
};
