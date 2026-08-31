<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignRecipient extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_SENDING = 'sending';
    public const STATUS_SENT = 'sent';
    public const STATUS_FAILED = 'failed';
    public const STATUS_SKIPPED_RECENTLY_SENT = 'skipped_recently_sent';
    public const STATUS_DUPLICATE_IN_FILE = 'duplicate_in_file';

    protected $fillable = [
        'campaign_id',
        'company',
        'email',
        'normalized_email',
        'vacancy',
        'contact_name',
        'contact_salutation',
        'subject_rendered',
        'message_rendered',
        'status',
        'sent_at',
        'failed_at',
        'skipped_at',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'failed_at' => 'datetime',
            'skipped_at' => 'datetime',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
