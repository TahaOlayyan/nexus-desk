<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    protected $fillable = [
        'company_id',
        'asset_id',
        'client_id',
        'tech_id',
        'category',
        'priority',
        'status',
        'description',
        'root_cause',
        'actions_taken',
        'worklog_notes',
        'sla_breached',
        'assigned_at',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'actions_taken' => 'array',
            'sla_breached' => 'boolean',
            'assigned_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tech_id');
    }
}
