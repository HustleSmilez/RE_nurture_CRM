<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Communication extends Model
{
    use HasFactory;

    const TYPE_EMAIL = 'email';
    const TYPE_SMS = 'sms';
    const TYPE_CALL = 'call';
    const TYPE_NOTE = 'note';

    const STATUS_SENT = 'sent';
    const STATUS_PENDING = 'pending';
    const STATUS_FAILED = 'failed';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_OPENED = 'opened';
    const STATUS_CLICKED = 'clicked';

    protected $fillable = [
        'contact_id',
        'lead_id',
        'type',
        'subject',
        'body',
        'status',
        'sent_at',
        'delivered_at',
        'opened_at',
        'clicked_at',
        'external_id',
        'metadata',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'opened_at' => 'datetime',
        'clicked_at' => 'datetime',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the contact for this communication.
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Get the lead for this communication (optional).
     */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    /**
     * Get communications by type.
     */
    public static function byType(string $type)
    {
        return static::where('type', $type);
    }

    /**
     * Get opened/engaged communications.
     */
    public static function engaged()
    {
        return static::whereIn('status', [
            self::STATUS_OPENED,
            self::STATUS_CLICKED,
        ]);
    }

    /**
     * Mark communication as delivered.
     */
    public function markDelivered(): bool
    {
        $this->status = self::STATUS_DELIVERED;
        $this->delivered_at = now();
        return $this->save();
    }

    /**
     * Mark communication as opened.
     */
    public function markOpened(): bool
    {
        $this->status = self::STATUS_OPENED;
        $this->opened_at = now();
        return $this->save();
    }

    /**
     * Mark communication as clicked.
     */
    public function markClicked(): bool
    {
        $this->status = self::STATUS_CLICKED;
        $this->clicked_at = now();
        return $this->save();
    }
}
