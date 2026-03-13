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
        Schema::create('works', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category');        // web | graphic | brand | ui
            $table->string('category_label');  // display name, e.g. "Web Application"
            $table->string('layout');          // pc--featured | pc--tall | pc--wide | …
            $table->json('tags')->nullable();  // ["Laravel","Tailwind CSS"]
            $table->string('image')->nullable();
            $table->string('link')->nullable();
            $table->unsignedSmallInteger('year')->default(2025);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('published')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('works');
    }
};
