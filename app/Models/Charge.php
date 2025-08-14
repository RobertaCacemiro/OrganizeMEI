<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Charge extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'mei_id',
        'client_id',
        'amount',
        'due_date',
        'payment_date',
        'status',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mei()
    {
        return $this->belongsTo(MeiProfile::class, 'mei_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
