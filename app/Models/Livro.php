<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Livro extends Model
{
    protected $table = 'livros';
    protected $primaryKey = 'id_livro';
    public $timestamps = false;

    protected $fillable = [
        'titulo', 'isbn', 'ano_publicacao', 'quantidade_exemplar',
        'categoria_id', 'editora_id', 'autor_id',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'categoria_id', 'id_categoria');
    }

    public function editora(): BelongsTo
    {
        return $this->belongsTo(Editora::class, 'editora_id', 'id_editora');
    }

    public function autor(): BelongsTo
    {
        return $this->belongsTo(Autor::class, 'autor_id', 'id_autor');
    }

    public function emprestimos(): BelongsToMany
    {
        return $this->belongsToMany(Emprestimo::class, 'itens_emprestimos', 'livro_id', 'emprestimo_id')
            ->withPivot('data_devolucao');
    }
}
