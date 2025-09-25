<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeiPixKey extends Model
{
    protected $fillable = [
        'mei_profile_id',
        'type',
        'key_value',
        'is_active',
    ];

    public function meiProfile()
    {
        return $this->belongsTo(MeiProfile::class);
    }
}
