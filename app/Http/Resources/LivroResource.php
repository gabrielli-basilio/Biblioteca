<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LivroResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_livro,
            'titulo' => $this->titulo,
            'isbn' => $this->isbn,
            'ano_publicacao' => $this->ano_publicacao,
            'quantidade_exemplar' => $this->quantidade_exemplar,
            'categoria' => $this->whenLoaded('categoria', fn () => $this->categoria ? [
                'id' => $this->categoria->id_categoria,
                'nome' => $this->categoria->nome,
            ] : null),
            'editora' => $this->whenLoaded('editora', fn () => [
                'id' => $this->editora->id_editora,
                'nome' => $this->editora->nome,
            ]),
            'autor' => $this->whenLoaded('autor', fn () => [
                'id' => $this->autor->id_autor,
                'nome' => $this->autor->nome,
            ]),
        ];
    }
}
