<?php

namespace App\Models;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\TicketComment;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable =[
        'asset_id',
        'ticket_category_id',
        'requester_employee_id',
        'assigned_to_user_id',
        'reference',
        'priority',
        'status',
        'description',
        'due_at',
        'resolved_at',
        'closed_at',
    ];

    protected $casts =[
        'priority'    => TicketPriority::class,
        'status'      => TicketStatus::class,
        'due_at'      => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at'   => 'datetime',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, 'ticket_category_id');
    }

    public function requester(): BelongsTo
    {
        // L'employé qui a créé le ticket
        return $this->belongsTo(Employee::class, 'requester_employee_id');
    }

    public function assignedTo(): BelongsTo
    {
        // Le technicien (User) en charge du ticket
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class);
    }
}