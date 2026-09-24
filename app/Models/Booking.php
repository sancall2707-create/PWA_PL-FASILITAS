<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'bookable_type',
        'bookable_id',
        'driver',
        'purpose',
        'organizer',
        'event_name',
        'event_description',
        'responsible_person',
        'responsible_phone',
        'requested_facilities_qty',
        'date_from',
        'date_to',
        'time_from',
        'time_to',
        'status',
        'confirmation_status',
        'contact_info',
    ];

    protected $casts = [
        'date_from' => 'date',
        'date_to' => 'date',
        'time_from' => 'datetime:H:i',
        'time_to' => 'datetime:H:i',
        'requested_facilities_qty' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function bookable(): MorphTo
    {
        return $this->morphTo();
    }
}