<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mei_id',
        'category_id',
        'transaction_date',
        'amount',
        'type',
        'description',
        'observation',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function meiProfile()
    {
        return $this->belongsTo(MeiProfile::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
