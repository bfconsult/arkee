<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Shared finish/stain reference list - referenced by both items_catalog
     * (a catalogue item's fixed finish) and furniture_schedule_lines (a
     * project's own finish choice for that line), rather than two
     * independent dropdowns.
     */
    public function up(): void
    {
        Schema::create('finishes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['timber_stain', 'fabric', 'other'])->default('other');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finishes');
    }
};
