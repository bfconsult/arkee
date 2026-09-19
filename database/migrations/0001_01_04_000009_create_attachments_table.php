<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Generic polymorphic attachment table (images, fabric/stain photos, PO
     * PDFs) - replaces Airtable's per-field attachment columns. Column names
     * (entity_type/entity_id) match the doc rather than Laravel's default
     * attachable_* morph convention - see Attachment::entity().
     */
    public function up(): void
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type');
            $table->unsignedBigInteger('entity_id');
            $table->enum('kind', ['image', 'fabric_stain', 'po_pdf']);
            $table->string('url');
            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
