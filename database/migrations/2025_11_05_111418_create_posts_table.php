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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            // Autor võib hiljem olla kustutatud ehk set null
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDeleted();
            //Kategooria pole kohustuslik
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDeleted();
            $table->string('title', 180);
            $table->string('slug', 220)->unique();
            $table->text('excerpt')->nullable(); // Sissejuhatav tekst
            $table->longText('body'); //Kohustuslik
            
            // Kasuta enum või strin + kontroll. MySQL sobib enum
            $table->enum('status', ['draft', 'review', 'published', 'archived'])->default('draft')->index();

            $table->dateTime('published_at')->nullable()->index();
            $table->string('featured_image', 255)->nullable(); // Pilt
            $table->unsignedSmallInteger('reading_time')->nullable(); // Lugemise aeg

            $table->timestamps();

            $table->softDeletes();

            //Avaliku vaate jaoks kiiremd päringud
            $table->index(['status', 'published_at']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
