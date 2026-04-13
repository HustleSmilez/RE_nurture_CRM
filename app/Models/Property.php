<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'property_type',
        'bedrooms',
        'bathrooms',
        'square_feet',
        'lot_size',
        'year_built',
        'price',
        'status',
        'description',
        'image_url',
        'listing_url',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'square_feet' => 'integer',
        'lot_size' => 'integer',
        'year_built' => 'integer',
        'bedrooms' => 'integer',
        'bathrooms' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get all leads interested in this property.
     */
    public function interestedLeads(): HasMany
    {
        return $this->hasMany(Lead::class, 'property_interest');
    }

    /**
     * Get full address.
     */
    public function getFullAddressAttribute(): string
    {
        return "{$this->address}, {$this->city}, {$this->state} {$this->zip_code}";
    }

    /**
     * Search properties.
     */
    public static function search(string $query)
    {
        return static::where('address', 'like', "%{$query}%")
            ->orWhere('city', 'like', "%{$query}%")
            ->orWhere('zip_code', 'like', "%{$query}%");
    }

    /**
     * Filter by property type.
     */
    public static function byType(string $type)
    {
        return static::where('property_type', $type);
    }

    /**
     * Filter by price range.
     */
    public static function priceRange(float $min, float $max)
    {
        return static::whereBetween('price', [$min, $max]);
    }
}
