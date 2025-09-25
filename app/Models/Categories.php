<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Categories extends Model
{
    use HasFactory;

    // Campos que podem ser preenchidos via mass assignment
    protected $fillable = [
        'user_id',
        'mei_id',
        'type',
        'name',
    ];

    public function getTypeNameAttribute()
    {
        return $this->type == 1 ? 'Despesa' : 'Receita';
    }

    // Relacionamento com o usuário
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relacionamento com o mei_profile
    public function meiProfile()
    {
        return $this->belongsTo(MeiProfile::class, 'mei_id');
    }
}
