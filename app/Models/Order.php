<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'guest_name', 'guest_email', 'guest_phone', 'total_amount', 'status',
    ];

    protected $casts = [
        'total_amount' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recipientEmail(): ?string
    {
        return $this->guest_email ?? $this->user?->email;
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
