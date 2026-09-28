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
        Schema::table('blogs', function (Blueprint $table) {
            // Add slug after title (unique, nullable for backward compat)
            $table->string('slug')->nullable()->unique()->after('title');

            // Alt text for the featured image
            $table->string('alt_text')->nullable()->after('image');

            // JSON-LD schema markup (stored without <script> tags)
            $table->json('schema_markup')->nullable()->after('faqs');

            // SEO meta fields
            $table->string('meta_title', 160)->nullable()->after('schema_markup');
            $table->text('meta_description')->nullable()->after('meta_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropColumn(['slug', 'alt_text', 'schema_markup', 'meta_title', 'meta_description']);
        });
    }
};
