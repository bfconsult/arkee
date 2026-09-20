<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('components', function (Blueprint $table) {
            // Nullable - a Fabric component (see is_fabric) has its Material
            // (and Finish) chosen later, per Project, not fixed on the Item.
            $table->foreignId('material_id')->nullable()->after('item_id')->constrained()->nullOnDelete();
            // Purchasing fields mirror Material's own - which level carries
            // them depends on the material (e.g. fabric is usually bought as
            // one material from one supplier; timber is often sourced per
            // component instead).
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->string('code_supplier')->nullable();
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->decimal('meterage', 8, 3)->nullable();
            // When true, this component's Material/Finish aren't fixed on
            // the Item - they're chosen per Furniture Schedule Line instead,
            // when the Item is actually added to a Project.
            $table->boolean('is_fabric')->default(false)->after('material_id');
        });
    }

    public function down(): void
    {
        Schema::table('components', function (Blueprint $table) {
            $table->dropConstrainedForeignId('material_id');
            $table->dropConstrainedForeignId('supplier_id');
            $table->dropColumn(['code_supplier', 'unit_cost', 'meterage', 'is_fabric']);
        });
    }
};
