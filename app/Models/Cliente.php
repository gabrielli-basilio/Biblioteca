<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    protected $table = 'clientes';
    protected $primaryKey = 'id_cliente';
    public $timestamps = false;

    protected $fillable = [
        'nome', 'cpf', 'telefone', 'email', 'cep', 'logradouro', 'numero_residencial',
    ];

    public function emprestimos(): HasMany
    {
        return $this->hasMany(Emprestimo::class, 'cliente_id', 'id_cliente');
    }
}
