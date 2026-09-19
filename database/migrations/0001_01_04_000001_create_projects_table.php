<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            // Auto-built per the doc (client + PM initials + sequence, etc.)
            // - computed in the application layer, just stored here.
            $table->string('quote_number')->nullable();
            $table->foreignId('client_id')->constrained();
            $table->foreignId('pm_user_id')->constrained('users');
            $table->string('project_descriptor')->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->date('date')->nullable();
            // Plain string, not an enum - the doc doesn't define a fixed set
            // of project statuses (unlike purchase_orders.order_status).
            $table->string('status')->nullable();
            $table->string('site_name')->nullable();
            $table->string('site_address')->nullable();
            $table->string('site_contact_name')->nullable();
            $table->unsignedInteger('next_po_sequence')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
