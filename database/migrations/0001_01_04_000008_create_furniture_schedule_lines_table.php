<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('furniture_schedule_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained();
            $table->foreignId('item_id')->constrained('items');
            $table->enum('row_type', ['parent', 'sub'])->default('parent');
            $table->foreignId('parent_line_id')->nullable()->constrained('furniture_schedule_lines')->nullOnDelete();
            $table->foreignId('fabric_supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->text('fabric_notes')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            // Meterage (from the chosen Material) x this project's fabric rate.
            $table->decimal('fabric_price_pm', 10, 2)->nullable();
            // Manual override of the markup-computed client price - see
            // markup_target_pct below.
            $table->decimal('price_override', 10, 2)->nullable();
            $table->decimal('markup_target_pct', 6, 2)->nullable();
            $table->date('required_by')->nullable();
            $table->foreignId('delivery_location_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('include_on_po')->default(false);
            $table->foreignId('colour_id')->nullable()->constrained('colours')->nullOnDelete();
            $table->decimal('internal_cost_manual', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('furniture_schedule_lines');
    }
};
