<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->foreignId('subject_id')
                ->constrained('subjects')
                ->cascadeOnDelete();

            $table->foreignId('chapter_id')
                ->constrained('chapters')
                ->cascadeOnDelete();

            $table->foreignId('bloom_level_id')
                ->constrained('bloom_levels')
                ->restrictOnDelete();

            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->text('content');
            $table->string('type');
            $table->text('explanation')->nullable();
            $table->string('status')->default('active');
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['subject_id']);
            $table->dropForeign(['chapter_id']);
            $table->dropForeign(['bloom_level_id']);
            $table->dropForeign(['created_by']);

            $table->dropColumn([
                'subject_id',
                'chapter_id',
                'bloom_level_id',
                'created_by',
                'content',
                'type',
                'explanation',
                'status',
            ]);
        });
    }
};