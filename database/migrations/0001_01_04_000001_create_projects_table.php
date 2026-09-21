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
            $table->foreignId('client_id')->constrained();
            $table->foreignId('pm_user_id')->constrained('users');
            $table->string('project_descriptor')->nullable();
            // quote_number/version/date/status live on the quotes table now -
            // a Project can have several Quotes, see create_quotes_table.
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
