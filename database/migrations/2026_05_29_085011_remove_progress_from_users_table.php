<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['last_book_id']);
            $table->dropColumn(['last_book_id', 'last_page']);
        });
    }
    
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignUuid('last_book_id')->nullable()->constrained('books')->onDelete('set null');
            $table->unsignedInteger('last_page')->nullable();
        });
    }
};
