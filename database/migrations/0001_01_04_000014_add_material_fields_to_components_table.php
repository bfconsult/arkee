<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('components', function (Blueprint $table) {
            $table->foreignId('material_id')->after('item_id')->constrained();
            // Purchasing fields mirror Material's own - which level carries
            // them depends on the material (e.g. fabric is usually bought as
            // one material from one supplier; timber is often sourced per
            // component instead).
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->string('code_supplier')->nullable();
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->decimal('meterage', 8, 3)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('components', function (Blueprint $table) {
            $table->dropConstrainedForeignId('material_id');
            $table->dropConstrainedForeignId('supplier_id');
            $table->dropColumn(['code_supplier', 'unit_cost', 'meterage']);
        });
    }
};
