<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('translation_key_id')
                ->constrained('translation_keys')
                ->cascadeOnDelete();
            $table->foreignId('locale_id')
                ->constrained('locales')
                ->cascadeOnDelete();
            $table->longText('value');
            $table->boolean('is_approved')->default(false)->index();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['translation_key_id', 'locale_id']);
            $table->index(['locale_id', 'is_approved']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};