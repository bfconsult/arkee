<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Many-to-many between purchase_orders and furniture_schedule_lines -
     * a schedule line can be included on more than one PO over its life
     * (e.g. reissued), and a PO groups many flagged lines by supplier.
     */
    public function up(): void
    {
        Schema::create('po_schedule_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('po_id')->constrained('purchase_orders')->cascadeOnDelete();
            $table->foreignId('schedule_line_id')->constrained('furniture_schedule_lines')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['po_id', 'schedule_line_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('po_schedule_lines');
    }
};
