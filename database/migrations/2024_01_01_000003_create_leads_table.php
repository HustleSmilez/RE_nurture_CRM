<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained()->onDelete('cascade');
            $table->foreignId('pipeline_id')->nullable()->constrained()->onDelete('set null');
            $table->string('status')->default('new'); // new, contacted, qualified, proposal, negotiation, closed, lost
            $table->decimal('value', 12, 2)->nullable();
            $table->string('property_interest')->nullable();
            $table->date('estimated_close_date')->nullable();
            $table->timestamp('last_contacted_at')->nullable();
            $table->string('source')->nullable();
            $table->longText('notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('contact_id');
            $table->index('pipeline_id');
            $table->index('status');
            $table->index('estimated_close_date');
            $table->index('last_contacted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
