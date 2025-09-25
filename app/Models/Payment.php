<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mei_id',
        'client_id',
        'charge_id',
        'status',
        'due_date',
        'amount',
        'payment_date',
        'sent',
        'resent_by',
        'key'
    ];

    protected static function booted()
    {
        static::creating(function ($payment) {
            if (empty($payment->key)) {
                $payment->key = Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mei()
    {
        return $this->belongsTo(MeiProfile::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function charge()
    {
        return $this->belongsTo(Charge::class);
    }

    public function resentBy()
    {
        return $this->belongsTo(User::class, 'resent_by');
    }
}
