<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Autor extends Model
{
    protected $table = 'autores';
    protected $primaryKey = 'id_autor';
    public $timestamps = false;
    protected $fillable = ['nome'];

    public function livros(): HasMany
    {
        return $this->hasMany(Livro::class, 'autor_id', 'id_autor');
    }
}
