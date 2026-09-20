<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // Only Fabric materials are offered on the Fabric Component
            // pick list when adding an Item to a Furniture Schedule Line.
            $table->boolean('is_fabric')->default(false);
            // Nullable - not every material has a single fixed supplier (e.g.
            // timber is often sourced per-component instead, see components).
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->string('code_supplier')->nullable();
            $table->decimal('unit_cost', 10, 2)->nullable();
            // Fabric meterage - how much of this material one unit consumes.
            $table->decimal('meterage', 8, 3)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
