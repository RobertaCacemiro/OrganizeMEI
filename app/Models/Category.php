<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mei_profile_id',
        'name',
    ];

    // 🔗 Relacionamento com o usuário (dono da categoria)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 🔗 Relacionamento com o MEI (perfil da empresa)
    public function meiProfile()
    {
        return $this->belongsTo(MeiProfile::class);
    }

    // 🔗 Relacionamento com as transações
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}

?>
