<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Editora extends Model
{
    protected $table = 'editoras';
    protected $primaryKey = 'id_editora';
    public $timestamps = false;
    protected $fillable = ['nome'];

    public function livros(): HasMany
    {
        return $this->hasMany(Livro::class, 'editora_id', 'id_editora');
    }
}
