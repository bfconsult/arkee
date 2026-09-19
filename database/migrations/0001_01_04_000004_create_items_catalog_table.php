<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items_catalog', function (Blueprint $table) {
            $table->id();
            $table->string('catalogue_no')->nullable();
            $table->enum('row_type', ['parent', 'sub'])->default('parent');
            $table->string('item_type')->nullable();
            $table->foreignId('supplier_id')->constrained();
            $table->string('code_supplier')->nullable();
            $table->text('notes_supplier')->nullable();
            $table->decimal('unit_cost', 10, 2)->nullable();
            // Fabric meterage - only meaningful on a Sub Item row.
            $table->decimal('meterage', 8, 3)->nullable();
            $table->foreignId('parent_item_id')->nullable()->constrained('items_catalog')->nullOnDelete();
            $table->unsignedInteger('height_mm')->nullable();
            $table->unsignedInteger('width_mm')->nullable();
            $table->unsignedInteger('depth_mm')->nullable();
            $table->string('packaging_type')->nullable();
            $table->foreignId('finish_id')->nullable()->constrained('finishes')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items_catalog');
    }
};
