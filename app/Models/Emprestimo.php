<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Emprestimo extends Model
{
    protected $table = 'emprestimos';
    protected $primaryKey = 'id_emprestimo';
    public $timestamps = false;

    protected $fillable = ['cliente_id', 'data_emprestimo', 'data_prevista_devolucao', 'status'];

    protected function casts(): array
    {
        return [
            'data_emprestimo' => 'date',
            'data_prevista_devolucao' => 'date',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id', 'id_cliente');
    }

    public function livros(): BelongsToMany
    {
        return $this->belongsToMany(Livro::class, 'itens_emprestimos', 'emprestimo_id', 'livro_id')
            ->withPivot('data_devolucao');
    }

    public function rotuloStatus(): string
    {
        return match ($this->status) {
            'em_andamento' => 'Em andamento',
            'devolvido' => 'Devolvido',
            'atrasado' => 'Atrasado',
            default => $this->status,
        };
    }
}
