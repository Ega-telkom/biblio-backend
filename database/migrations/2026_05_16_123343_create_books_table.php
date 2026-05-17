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
        Schema::create('books', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('genre_id')->constrained('genres')->onDelete('restrict'); // cascade terlalu agresif untuk buku
            $table->string('title');
            $table->string('isbn')->unique()->nullable();  // nullable karena tidak semua buku punya
            $table->text('description')->nullable();
            $table->string('author');
            $table->string('publisher')->nullable();
            $table->string('lang', 5)->default('id');      // kecilkan, cukup 'id', 'en', dll
            $table->date('published_date')->nullable();    // ganti ke date, bukan string
            $table->string('format');
            $table->unsignedInteger('page_count')->nullable();
            $table->unsignedBigInteger('price');           // hindari integer negatif untuk harga
            $table->string('cover_url')->nullable();       // saran tambahan
            $table->string('file_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
