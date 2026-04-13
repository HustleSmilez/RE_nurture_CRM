<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('communications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained()->onDelete('cascade');
            $table->foreignId('lead_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('type'); // email, sms, call, note
            $table->string('subject')->nullable();
            $table->longText('body');
            $table->string('status')->default('pending'); // sent, pending, failed, delivered, opened, clicked
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->string('external_id')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('contact_id');
            $table->index('lead_id');
            $table->index('type');
            $table->index('status');
            $table->index('sent_at');
            $table->unique('external_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('communications');
    }
};
