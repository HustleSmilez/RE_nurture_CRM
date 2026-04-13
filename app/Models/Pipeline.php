<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pipeline extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get all leads in this pipeline.
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    /**
     * Get pipeline statistics.
     */
    public function getStats()
    {
        return [
            'total_leads' => $this->leads()->count(),
            'total_value' => $this->leads()->sum('value'),
            'new' => $this->leads()->where('status', Lead::STATUS_NEW)->count(),
            'contacted' => $this->leads()->where('status', Lead::STATUS_CONTACTED)->count(),
            'qualified' => $this->leads()->where('status', Lead::STATUS_QUALIFIED)->count(),
            'proposal' => $this->leads()->where('status', Lead::STATUS_PROPOSAL)->count(),
            'negotiation' => $this->leads()->where('status', Lead::STATUS_NEGOTIATION)->count(),
            'closed' => $this->leads()->where('status', Lead::STATUS_CLOSED)->count(),
            'lost' => $this->leads()->where('status', Lead::STATUS_LOST)->count(),
        ];
    }
}
