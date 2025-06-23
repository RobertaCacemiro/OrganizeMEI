<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class PerfilMEI extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_usuario',
        'cnpj',
        'nome',
        'razao',
        'dat_abertura',
        'cod_cnae',
        'cep',
        'rua',
        'numero',
        'complemento',
        'cod_cep',
        'cidade',
        'estado'
    ];
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
}
