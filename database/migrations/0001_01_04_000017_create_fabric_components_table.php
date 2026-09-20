<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The Material/Finish chosen for one Fabric Component of an Item, on one
     * Furniture Schedule Line - a Fabric Component has no fixed Material of
     * its own (see components.is_fabric), so this is where that choice is
     * actually recorded, per Project.
     */
    public function up(): void
    {
        Schema::create('fabric_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('furniture_schedule_line_id')->constrained()->cascadeOnDelete();
            $table->foreignId('component_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('finish_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->unique(['furniture_schedule_line_id', 'component_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fabric_components');
    }
};
