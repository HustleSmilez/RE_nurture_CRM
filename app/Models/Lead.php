<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    use HasFactory;

    const STATUS_NEW = 'new';
    const STATUS_CONTACTED = 'contacted';
    const STATUS_QUALIFIED = 'qualified';
    const STATUS_PROPOSAL = 'proposal';
    const STATUS_NEGOTIATION = 'negotiation';
    const STATUS_CLOSED = 'closed';
    const STATUS_LOST = 'lost';

    protected $fillable = [
        'contact_id',
        'pipeline_id',
        'status',
        'value',
        'property_interest',
        'estimated_close_date',
        'last_contacted_at',
        'source',
        'notes',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'last_contacted_at' => 'datetime',
        'estimated_close_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the contact for this lead.
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Get the pipeline for this lead.
     */
    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(Pipeline::class);
    }

    /**
     * Get all tasks for this lead.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Move lead to next stage in pipeline.
     */
    public function advance(): bool
    {
        $statuses = [
            self::STATUS_NEW,
            self::STATUS_CONTACTED,
            self::STATUS_QUALIFIED,
            self::STATUS_PROPOSAL,
            self::STATUS_NEGOTIATION,
            self::STATUS_CLOSED,
        ];

        $currentIndex = array_search($this->status, $statuses);

        if ($currentIndex !== false && $currentIndex < count($statuses) - 1) {
            $this->status = $statuses[$currentIndex + 1];
            return $this->save();
        }

        return false;
    }

    /**
     * Mark as lost.
     */
    public function markAsLost(string $reason = null): bool
    {
        $this->status = self::STATUS_LOST;
        if ($reason) {
            $this->notes = ($this->notes ? $this->notes . "\n" : '') . "Lost: {$reason}";
        }
        return $this->save();
    }

    /**
     * Mark as closed/won.
     */
    public function markAsClosed(): bool
    {
        $this->status = self::STATUS_CLOSED;
        return $this->save();
    }

    /**
     * Get age of lead in days.
     */
    public function getAgeInDaysAttribute(): int
    {
        return $this->created_at->diffInDays(now());
    }
}
