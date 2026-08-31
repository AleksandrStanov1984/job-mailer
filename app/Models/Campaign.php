<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    protected $fillable = [
        'name',
        'subject',
        'template_original_name',
        'duplicate_protection_enabled',
        'duplicate_protection_days',
        'delay_seconds',
        'started_at',
        'finished_at',
    ];

    protected function casts(): array
    {
        return [
            'duplicate_protection_enabled' => 'boolean',
            'duplicate_protection_days' => 'integer',
            'delay_seconds' => 'integer',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(CampaignRecipient::class);
    }
}
