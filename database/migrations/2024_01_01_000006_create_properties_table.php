<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('address');
            $table->string('city');
            $table->string('state');
            $table->string('zip_code');
            $table->string('country')->default('USA');
            $table->string('property_type'); // single_family, condo, townhouse, multi_family, land, etc.
            $table->unsignedInteger('bedrooms')->nullable();
            $table->float('bathrooms')->nullable();
            $table->unsignedInteger('square_feet')->nullable();
            $table->unsignedInteger('lot_size')->nullable();
            $table->unsignedInteger('year_built')->nullable();
            $table->decimal('price', 14, 2)->nullable();
            $table->string('status')->default('active'); // active, pending, sold, off_market
            $table->longText('description')->nullable();
            $table->string('image_url')->nullable();
            $table->string('listing_url')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('city');
            $table->index('state');
            $table->index('zip_code');
            $table->index('status');
            $table->fullText(['address', 'city', 'state']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
