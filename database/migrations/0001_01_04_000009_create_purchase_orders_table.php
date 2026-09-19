<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            // Project + supplier code + sequence + PM initials, per the doc -
            // computed in the application layer, just stored here.
            $table->string('po_number')->nullable();
            $table->foreignId('project_id')->constrained();
            $table->foreignId('supplier_id')->constrained();
            $table->foreignId('delivery_location_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assignee_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('order_status', ['draft', 'sent', 'confirmed', 'received'])->default('draft');
            $table->date('date_issued')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
