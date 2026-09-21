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
            $table->foreignId('quote_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items');
            $table->text('fabric_notes')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            // Manual override of the markup-computed client price - see
            // markup_target_pct below.
            $table->decimal('price_override', 10, 2)->nullable();
            $table->decimal('markup_target_pct', 6, 2)->nullable();
            $table->date('required_by')->nullable();
            $table->boolean('include_on_po')->default(false);
            $table->decimal('internal_cost_manual', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('furniture_schedule_lines');
    }
};
