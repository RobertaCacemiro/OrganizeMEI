<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mei_id',
        'cpf_cnpj',
        'name',
        'email',
        'phone',
        'street',
        'number',
        'complement',
        'district',
        'city',
        'state',
        'zip_code',
        'notes',
    ];

    // Relacionamento com User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // // Relacionamento com MeiProfile
    // public function mei()
    // {
    //     return $this->belongsTo(MeiProfile::class);
    // }
}
