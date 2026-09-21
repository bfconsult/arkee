<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A Project can have several Quotes (e.g. a revised proposal) - each
     * Quote owns its own Furniture Schedule Lines, see
     * create_furniture_schedule_lines_table. Purchase Orders stay directly
     * on the Project, not any one Quote.
     */
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            // Auto-built per the doc (client + PM initials + sequence, etc.)
            // - computed in the application layer, just stored here.
            $table->string('quote_number')->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->date('date')->nullable();
            $table->enum('status', ['quote', 'complete', 'approved', 'cancelled'])->default('quote');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
