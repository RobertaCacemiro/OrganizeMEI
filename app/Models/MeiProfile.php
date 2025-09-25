<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeiProfile extends Model
{
    use HasFactory;

    protected $table = 'mei_profile';

    protected $fillable = [
        'identification',
        'cnpj',
        'email',
        'phone',
        'street',
        'number',
        'complement',
        'district',
        'city',
        'state',
        'zip_code',
        'profile_photo',
    ];

    // Relacionamento com User
    public function users()
    {
        return $this->belongsToMany(User::class, 'mei_profile_user')
            ->withTimestamps();
    }

    public function clients()
    {
        return $this->hasMany(Client::class, 'mei_id');
    }

    public function pixKeys()
    {
        return $this->hasMany(\App\Models\MeiPixKey::class, 'mei_profile_id');
    }

    public function activePixKey()
    {
        return $this->hasOne(\App\Models\MeiPixKey::class, 'mei_profile_id')
            ->where('is_active', 1);
    }
}
