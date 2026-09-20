<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The Material/Finish chosen for one Component of an Item, on one
     * Furniture Schedule Line. For a Fabric component (components.is_fabric)
     * the Material itself is also chosen here, since it has none of its
     * own. For a regular component, material_id just mirrors the
     * component's own fixed material - only finish_id is actually a choice.
     */
    public function up(): void
    {
        Schema::create('component_finishes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('furniture_schedule_line_id')->constrained()->cascadeOnDelete();
            $table->foreignId('component_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('finish_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            // Explicit short name - the auto-generated one built from this
            // (longer) table name exceeds MySQL's 64-char identifier limit.
            $table->unique(['furniture_schedule_line_id', 'component_id'], 'component_finishes_line_component_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('component_finishes');
    }
};
