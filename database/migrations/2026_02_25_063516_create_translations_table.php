<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('group', 50)->index();
            $table->string('key');
            $table->string('locale', 5)->index();
            $table->text('value');
            $table->timestamps();

            $table->unique(['key', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
